<?php
/*
|--------------------------------------------------------------------------
| app/Http/Controllers/DeclarationController.php
|--------------------------------------------------------------------------
| Remplace le contrôleur placeholder actuel.
*/

namespace App\Http\Controllers;

use App\Contribuable;
use App\TrackedDocType;
use App\DeclarationStatut;
use Illuminate\Http\Request;

class DeclarationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Page HTML "Déclaration" */
    public function page()
    {
        return view('declarations.index', self::donneesSuivi());
    }

    /**
     * Données de suivi partagées par les écrans Déclaration ET Documents
     * (l'écran Documents en a besoin pour la liste des documents à suivre).
     */
    public static function donneesSuivi()
    {
        return [
            'initialContribuables'      => Contribuable::latest()->get(),
            'initialTrackedDocTypes'    => TrackedDocType::orderBy('nom')->get()
                                             ->map(function ($t) { return $t->toFront(); })->values(),
            'initialDeclarationStatuts' => DeclarationStatut::all(['contribuable_id', 'tracked_doc_type_id', 'statut']),
        ];
    }

    // ================= DOCUMENTS À SUIVRE =================

    public function storeTrackedDocType(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:120|unique:tracked_doc_types,nom',
        ]);

        $type = TrackedDocType::create(['nom' => $data['nom']]);

        return response()->json(['trackedDocType' => $type->toFront()]);
    }

    public function destroyTrackedDocType($id)
    {
        $type = TrackedDocType::findOrFail($id);
        $type->delete(); // les statuts liés sont supprimés en cascade

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

        return response()->json(['trackedDocType' => $type->toFront()]);
    }

    // ================= STATUTS DU TABLEAU =================

    /** Crée, met à jour ou supprime le statut d'une case du tableau */
    public function storeStatut(Request $request)
    {
        $data = $request->validate([
            'contribuable_id'     => 'required|integer|exists:contribuables,id',
            'tracked_doc_type_id' => 'required|integer|exists:tracked_doc_types,id',
            'statut'              => 'required|in:aucun,non_declare,declare,penalite',
        ]);

        $where = [
            'contribuable_id'     => $data['contribuable_id'],
            'tracked_doc_type_id' => $data['tracked_doc_type_id'],
        ];

        // "aucun" = le contribuable n'est pas concerné par ce document : on retire la ligne
        if ($data['statut'] === 'aucun') {
            DeclarationStatut::where($where)->delete();
            return response()->json(['success' => true, 'statut' => 'aucun']);
        }

        // 'penalite' n'est jamais stocké : c'est un état calculé à partir de la date limite.
        $statut = $data['statut'] === 'penalite' ? 'non_declare' : $data['statut'];

        $ligne = DeclarationStatut::firstOrNew($where);
        $ligne->statut = $statut;
        $ligne->save();

        return response()->json(['success' => true, 'statut' => $statut]);
    }

    // ============ ORGANISME & LIEN DE VÉRIFICATION ============

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

        // Sécurité métier : pas de lien tant qu'aucun document à suivre n'est associé
        $aDesDocsSuivis = DeclarationStatut::where('contribuable_id', $c->id)->exists();
        if (! $aDesDocsSuivis && ! empty($data['lien_verification'])) {
            return response()->json([
                'message' => "Enregistrez d'abord un document à suivre pour ce contribuable.",
            ], 422);
        }

        $c->lien_verification = isset($data['lien_verification']) ? $data['lien_verification'] : null;
        $c->save();

        return response()->json(['contribuable' => $c]);
    }
}