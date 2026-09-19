<?php

namespace App\Http\Controllers;

use App\Contribuable;
use App\Document;
use App\Obligation;
use App\TrackedDocType;
use App\Services\FiscalSuiviService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DeclarationController extends Controller
{
    protected $suivi;

    public function __construct(FiscalSuiviService $suivi)
    {
        $this->middleware('auth');
        $this->suivi = $suivi;
    }

    public function page()
    {
        return view('declarations.index', self::donneesSuivi());
    }

    public static function donneesSuivi()
    {
        $suivi = app(FiscalSuiviService::class);

        return [
            'initialContribuables'      => Contribuable::latest()->get(),
            'initialTrackedDocTypes'    => TrackedDocType::orderBy('nom')->get()
                                             ->map(function ($t) { return $t->toFront(); })->values(),
            'initialDeclarationStatuts' => \App\DeclarationStatut::all(['contribuable_id', 'tracked_doc_type_id', 'statut']),
            'initialObligations'        => $suivi->listObligationsForFront(),
            'initialNotifications'      => $suivi->listNotificationsForFront(),
            'suiviKpis'                 => $suivi->kpis(),
        ];
    }

    // ================= TYPES À SUIVRE =================

    public function storeTrackedDocType(Request $request)
    {
        $data = $request->validate([
            'nom'              => 'required|string|max:120|unique:tracked_doc_types,nom',
            'periodicite'      => 'nullable|in:libre,mensuelle,trimestrielle,annuelle',
            'organisme_defaut' => 'nullable|string|max:40',
            'date_limite'      => 'nullable|date',
        ]);

        $type = TrackedDocType::create([
            'nom'              => $data['nom'],
            'periodicite'      => isset($data['periodicite']) ? $data['periodicite'] : 'libre',
            'organisme_defaut' => isset($data['organisme_defaut']) ? $data['organisme_defaut'] : null,
            'date_limite'      => isset($data['date_limite']) ? $data['date_limite'] : null,
        ]);

        return response()->json(['trackedDocType' => $type->toFront()], 201);
    }

    public function updateTrackedDocType(Request $request, $id)
    {
        $type = TrackedDocType::findOrFail($id);

        $data = $request->validate([
            'nom'              => 'required|string|max:120|unique:tracked_doc_types,nom,'.$type->id,
            'periodicite'      => 'nullable|in:libre,mensuelle,trimestrielle,annuelle',
            'organisme_defaut' => 'nullable|string|max:40',
            'date_limite'      => 'nullable|date',
        ]);

        $type->nom = $data['nom'];
        $type->periodicite = isset($data['periodicite']) ? $data['periodicite'] : 'libre';
        $type->organisme_defaut = isset($data['organisme_defaut']) ? $data['organisme_defaut'] : null;
        $type->date_limite = array_key_exists('date_limite', $data) ? $data['date_limite'] : $type->date_limite;
        $type->save();

        return response()->json(['trackedDocType' => $type->toFront()]);
    }

    public function destroyTrackedDocType($id)
    {
        $type = TrackedDocType::findOrFail($id);

        if ($type->obligations()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer ce type : des obligations y sont encore rattachées.',
            ], 422);
        }

        $type->delete();

        return response()->json(['success' => true]);
    }

    public function updateDeadline(Request $request, $id)
    {
        $data = $request->validate([
            'date_limite' => 'nullable|date',
        ]);

        $type = TrackedDocType::findOrFail($id);
        $type->date_limite = isset($data['date_limite']) ? $data['date_limite'] : null;
        $type->save();

        Obligation::where('tracked_doc_type_id', $type->id)
            ->whereNull('date_limite')
            ->where('statut', Obligation::STATUT_A_DECLARER)
            ->update(['date_limite' => $type->date_limite]);

        $this->suivi->syncNotifications();

        return response()->json(['trackedDocType' => $type->toFront()]);
    }

    // ================= OBLIGATIONS =================

    public function indexObligations()
    {
        return response()->json([
            'obligations' => $this->suivi->listObligationsForFront(),
            'kpis'        => $this->suivi->kpis(),
        ]);
    }

    public function storeObligation(Request $request)
    {
        $data = $request->validate([
            'contribuable_id'     => 'required|integer|exists:contribuables,id',
            'tracked_doc_type_id' => 'required|integer|exists:tracked_doc_types,id',
            'annee'               => 'required|integer|min:2000|max:2100',
            'periode'             => 'required|string|max:20',
            'date_limite'         => 'nullable|date',
            'organisme'           => 'nullable|string|max:120',
            'statut'              => 'nullable|in:a_declarer,declare,justificatif_depose',
        ]);

        $type = TrackedDocType::findOrFail($data['tracked_doc_type_id']);
        $periode = strtoupper(trim($data['periode']));

        $dateLimite = isset($data['date_limite']) && $data['date_limite']
            ? $data['date_limite']
            : Obligation::suggestDeadline($data['annee'], $periode, $type->periodicite);

        $contrib = Contribuable::findOrFail($data['contribuable_id']);
        $organisme = isset($data['organisme']) && $data['organisme']
            ? $data['organisme']
            : ($type->organisme_defaut ?: $contrib->organisme);

        $obligation = Obligation::create([
            'contribuable_id'     => $data['contribuable_id'],
            'tracked_doc_type_id' => $data['tracked_doc_type_id'],
            'annee'               => $data['annee'],
            'periode'             => $periode,
            'date_limite'         => $dateLimite,
            'statut'              => Obligation::STATUT_A_DECLARER,
            'organisme'           => $organisme,
        ]);

        $this->suivi->mirrorDeclarationStatut($obligation);
        $this->suivi->syncNotifications();

        return response()->json([
            'obligation' => $obligation->load(['contribuable', 'trackedDocType', 'documents'])->toFront(),
            'kpis'       => $this->suivi->kpis(),
        ], 201);
    }

    public function updateObligation(Request $request, $id)
    {
        $obligation = Obligation::with('documents')->findOrFail($id);

        $data = $request->validate([
            'date_limite'          => 'nullable|date',
            'date_depot'           => 'nullable|date',
            'statut'               => 'nullable|in:a_declarer,declare,justificatif_depose',
            'organisme'            => 'nullable|string|max:120',
            'resultat_controle'    => 'nullable|in:en_regle,non_conforme',
            'commentaire_controle' => 'nullable|string|max:2000',
            'date_controle'        => 'nullable|date',
            'annee'                => 'nullable|integer|min:2000|max:2100',
            'periode'              => 'nullable|string|max:20',
        ]);

        if (array_key_exists('statut', $data) && $data['statut'] !== null) {
            $this->assertStatutAllowed($obligation, $data['statut']);
            $obligation->statut = $data['statut'];
            if (in_array($data['statut'], [Obligation::STATUT_DECLARE, Obligation::STATUT_JUSTIFICATIF], true)
                && ! $obligation->date_depot) {
                $obligation->date_depot = now()->toDateString();
            }
        }

        foreach (['date_limite', 'date_depot', 'organisme', 'resultat_controle', 'commentaire_controle', 'date_controle', 'annee'] as $field) {
            if (array_key_exists($field, $data)) {
                $obligation->{$field} = $data[$field];
            }
        }
        if (isset($data['periode'])) {
            $obligation->periode = strtoupper($data['periode']);
        }

        $obligation->save();
        $this->suivi->mirrorDeclarationStatut($obligation);
        $this->suivi->syncNotifications();

        return response()->json([
            'obligation' => $obligation->fresh(['contribuable', 'trackedDocType', 'documents'])->toFront(),
            'kpis'       => $this->suivi->kpis(),
        ]);
    }

    public function destroyObligation($id)
    {
        $obligation = Obligation::findOrFail($id);
        $obligation->delete();
        $this->suivi->syncNotifications();

        return response()->json(['success' => true, 'kpis' => $this->suivi->kpis()]);
    }

    /**
     * Dépôt d'un justificatif directement sur une obligation.
     */
    public function attachJustificatif(Request $request, $id)
    {
        $obligation = Obligation::findOrFail($id);

        $data = $request->validate([
            'nom'     => 'nullable|string|max:255',
            'fichier' => 'required|file|max:10240|mimes:pdf,png,jpg,jpeg',
        ]);

        $file = $request->file('fichier');
        $doc = new Document();
        $doc->nom = isset($data['nom']) && $data['nom']
            ? $data['nom']
            : ('Justificatif — '.$obligation->trackedDocType->nom.' '.$obligation->periode.' '.$obligation->annee);
        $doc->type = $obligation->trackedDocType->nom;
        $doc->contribuable_id = $obligation->contribuable_id;
        $doc->obligation_id = $obligation->id;
        $doc->fournisseur = $obligation->organisme;
        $doc->montant = 0;
        $doc->fichier_path = $file->store('documents', 'public');
        $doc->fichier_nom = $file->getClientOriginalName();
        $doc->fichier_mime = $file->getClientMimeType();
        $doc->save();

        // Dès qu'une pièce est jointe, on peut passer en justificatif_depose si déjà déclaré,
        // sinon on reste a_declarer (le comptable marque déclaré manuellement).
        if ($obligation->statut === Obligation::STATUT_DECLARE) {
            $obligation->statut = Obligation::STATUT_JUSTIFICATIF;
            $obligation->save();
        }

        $this->suivi->mirrorDeclarationStatut($obligation);
        $this->suivi->syncNotifications();

        return response()->json([
            'document'   => $doc->load('contribuable')->toFront(),
            'obligation' => $obligation->fresh(['contribuable', 'trackedDocType', 'documents'])->toFront(),
            'kpis'       => $this->suivi->kpis(),
        ], 201);
    }

    // ================= LEGACY MATRIX STATUTS =================

    public function storeStatut(Request $request)
    {
        $data = $request->validate([
            'contribuable_id'     => 'required|integer|exists:contribuables,id',
            'tracked_doc_type_id' => 'required|integer|exists:tracked_doc_types,id',
            'statut'              => 'required|in:aucun,non_declare,declare,penalite,a_declarer,justificatif_depose',
            'annee'               => 'nullable|integer|min:2000|max:2100',
            'periode'             => 'nullable|string|max:20',
        ]);

        $annee = isset($data['annee']) ? (int) $data['annee'] : (int) date('Y');
        $periode = isset($data['periode']) ? strtoupper($data['periode']) : 'AUTRE';

        if ($data['statut'] === 'aucun') {
            Obligation::where([
                'contribuable_id'     => $data['contribuable_id'],
                'tracked_doc_type_id' => $data['tracked_doc_type_id'],
                'annee'               => $annee,
                'periode'             => $periode,
            ])->delete();

            \App\DeclarationStatut::where([
                'contribuable_id'     => $data['contribuable_id'],
                'tracked_doc_type_id' => $data['tracked_doc_type_id'],
            ])->delete();

            $this->suivi->syncNotifications();

            return response()->json(['success' => true, 'statut' => 'aucun']);
        }

        $map = [
            'non_declare' => Obligation::STATUT_A_DECLARER,
            'a_declarer' => Obligation::STATUT_A_DECLARER,
            'penalite' => Obligation::STATUT_A_DECLARER,
            'declare' => Obligation::STATUT_DECLARE,
            'justificatif_depose' => Obligation::STATUT_JUSTIFICATIF,
        ];
        $statut = $map[$data['statut']];

        $type = TrackedDocType::findOrFail($data['tracked_doc_type_id']);
        $obligation = Obligation::firstOrNew([
            'contribuable_id'     => $data['contribuable_id'],
            'tracked_doc_type_id' => $data['tracked_doc_type_id'],
            'annee'               => $annee,
            'periode'             => $periode,
        ]);

        if (! $obligation->exists) {
            $obligation->date_limite = $type->date_limite
                ?: Obligation::suggestDeadline($annee, $periode, $type->periodicite);
            $obligation->organisme = $type->organisme_defaut;
        }

        $this->assertStatutAllowed($obligation, $statut);
        $obligation->statut = $statut;
        if ($obligation->isCloturee() && ! $obligation->date_depot) {
            $obligation->date_depot = now()->toDateString();
        }
        $obligation->save();

        $this->suivi->mirrorDeclarationStatut($obligation);
        $this->suivi->syncNotifications();

        return response()->json([
            'success'    => true,
            'statut'     => $statut,
            'obligation' => $obligation->fresh(['contribuable', 'trackedDocType', 'documents'])->toFront(),
        ]);
    }

    public function updateOrganisme(Request $request, $id)
    {
        $data = $request->validate([
            'organisme' => 'nullable|string|max:120',
        ]);

        $c = Contribuable::findOrFail($id);
        $c->organisme = isset($data['organisme']) ? $data['organisme'] : null;
        $c->save();

        return response()->json(['contribuable' => $c]);
    }

    public function updateLienVerification(Request $request, $id)
    {
        $data = $request->validate([
            'lien_verification' => 'nullable|url|max:500',
        ]);

        $c = Contribuable::findOrFail($id);

        $aDesObligations = Obligation::where('contribuable_id', $c->id)->exists();
        if (! $aDesObligations && ! empty($data['lien_verification'])) {
            return response()->json([
                'message' => "Créez d'abord une obligation de suivi pour ce contribuable.",
            ], 422);
        }

        $c->lien_verification = isset($data['lien_verification']) ? $data['lien_verification'] : null;
        $c->save();

        return response()->json(['contribuable' => $c]);
    }

    protected function assertStatutAllowed(Obligation $obligation, $statut)
    {
        if (in_array($statut, [Obligation::STATUT_DECLARE, Obligation::STATUT_JUSTIFICATIF], true)) {
            $hasFile = $obligation->exists
                ? $obligation->hasJustificatif()
                : false;

            if (! $hasFile) {
                throw ValidationException::withMessages([
                    'statut' => "Impossible de clôturer l'obligation sans justificatif GED rattaché (fichier obligatoire).",
                ]);
            }
        }
    }
}
