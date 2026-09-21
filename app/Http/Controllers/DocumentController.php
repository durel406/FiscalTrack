<?php

namespace App\Http\Controllers;

use App\Document;
use App\Contribuable;
use App\TrackedDocType;
use App\Obligation;
use App\Services\FiscalSuiviService;
use App\Services\CloudFileStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected $suivi;
    protected $files;

    public function __construct(FiscalSuiviService $suivi, CloudFileStorage $files)
    {
        $this->middleware('auth');
        $this->suivi = $suivi;
        $this->files = $files;
    }

    public function page()
    {
        $data = DeclarationController::donneesSuivi();
        $data['initialDocuments'] = Document::with('contribuable')->actifs()->latest()->get()
                                        ->map(function ($d) { return $d->toFront(); })->values();

        return view('documents.index', $data);
    }

    public function archivesPage()
    {
        return view('archives.index', [
            'initialArchives'      => Document::with('contribuable')->archives()->latest('archived_at')->get()
                                        ->map(function ($d) { return $d->toFront(); })->values(),
            'initialContribuables' => Contribuable::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $files = $request->file('fichiers', []);
        if (!$files && $request->hasFile('fichier')) $files = [$request->file('fichier')];
        $documents = [];
        foreach ($files as $file) {
            $doc = new Document();
            $doc->fill($this->fields($data));
            $this->attachFile($file, $doc);
            $doc->nom = count($files) > 1 ? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) : $data['nom'];
            $doc->save();
            $this->lierObligation($doc, $data);
            $documents[] = $doc->load('contribuable')->toFront();
        }

        return response()->json(['success' => true, 'message' => count($documents).' document(s) enregistré(s) avec succès.', 'documents' => $documents, 'document' => $documents[0] ?? null], 201);
    }

    public function update(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        $data = $this->validated($request, false);

        $doc->fill($this->fields($data));
        $replacementFiles = $request->file('fichiers', []);
        $replacement = $replacementFiles[0] ?? $request->file('fichier');
        if ($replacement) $this->attachFile($replacement, $doc);
        $doc->save();
        $this->lierObligation($doc, $data);

        return response()->json(['success' => true, 'message' => 'Document modifié avec succès.', 'document' => $doc->load('contribuable')->toFront()]);
    }

    public function archive($id)
    {
        $doc = Document::findOrFail($id);
        $doc->archived_at = now();
        $doc->save();

        return response()->json(['success' => true, 'message' => 'Document archivé avec succès.', 'document' => $doc->load('contribuable')->toFront()]);
    }

    public function restore($id)
    {
        $doc = Document::findOrFail($id);
        $doc->archived_at = null;
        $doc->save();

        return response()->json(['success' => true, 'message' => 'Document restauré avec succès.', 'document' => $doc->load('contribuable')->toFront()]);
    }

    public function destroy($id)
    {
        $doc = Document::findOrFail($id);

        $this->files->delete($doc->fichier_path, $doc->fichier_disk);
        $doc->delete();

        return response()->json(['success' => true, 'message' => 'Document supprimé avec succès.']);
    }

    public function fichier($id)
    {
        $doc = Document::findOrFail($id);

        if (! $doc->fichier_path || ! Storage::disk('public')->exists($doc->fichier_path)) {
            abort(404, 'Aucun fichier associé à ce document.');
        }

        return response()->file(
            storage_path('app/public/'.$doc->fichier_path),
            ['Content-Type' => $doc->fichier_mime ?: 'application/octet-stream']
        );
    }

    private function validated(Request $request, bool $creating)
    {
        $rules = [
            'nom'             => 'required|string|max:255',
            'type'            => 'nullable|string|max:120',
            'fournisseur'     => 'nullable|string|max:255',
            'montant'         => 'nullable|integer|min:0',
            'contribuable_id' => 'nullable|integer|exists:contribuables,id',
            'obligation_id'   => 'nullable|integer|exists:obligations,id',
            'fichier'         => 'nullable|file|max:10240|mimes:pdf,png,jpg,jpeg',
            'fichiers'        => ($creating ? 'required|array|min:1' : 'nullable|array').'|max:20',
            'fichiers.*'      => 'file|max:10240|mimes:pdf,png,jpg,jpeg',
        ];

        return $request->validate($rules);
    }

    private function fields(array $data)
    {
        return [
            'nom'             => $data['nom'],
            'type'            => isset($data['type']) ? $data['type'] : null,
            'fournisseur'     => isset($data['fournisseur']) ? $data['fournisseur'] : null,
            'montant'         => isset($data['montant']) ? $data['montant'] : 0,
            'contribuable_id' => isset($data['contribuable_id']) ? $data['contribuable_id'] : null,
            'obligation_id'   => isset($data['obligation_id']) ? $data['obligation_id'] : null,
        ];
    }

    private function attachFile($file, Document $doc)
    {
        $this->files->delete($doc->fichier_path, $doc->fichier_disk);
        $stored = $this->files->put($file);
        $doc->fichier_path = $stored['path'];
        $doc->fichier_url = $stored['url'];
        $doc->fichier_disk = $stored['disk'];
        $doc->fichier_nom  = $file->getClientOriginalName();
        $doc->fichier_mime = $file->getClientMimeType();
    }

    /**
     * Rattache le document à une obligation existante (explicite ou déduite).
     * Ne crée plus d'obligation « silencieuse » : l'obligation doit exister.
     */
    private function lierObligation(Document $doc, array $data)
    {
        if (! empty($data['obligation_id'])) {
            $obligation = Obligation::find($data['obligation_id']);
            if ($obligation) {
                $doc->obligation_id = $obligation->id;
                if (! $doc->contribuable_id) {
                    $doc->contribuable_id = $obligation->contribuable_id;
                }
                $doc->save();
                $this->suivi->syncNotifications();
            }

            return;
        }

        if (! $doc->contribuable_id || ! $doc->type) {
            return;
        }

        $type = TrackedDocType::whereRaw('LOWER(TRIM(nom)) = ?', [mb_strtolower(trim($doc->type))])->first();
        if (! $type) {
            return;
        }

        // Rattacher à l'obligation ouverte la plus urgente du même type / contribuable
        $obligation = Obligation::where('contribuable_id', $doc->contribuable_id)
            ->where('tracked_doc_type_id', $type->id)
            ->whereNotIn('statut', [Obligation::STATUT_JUSTIFICATIF])
            ->orderByRaw("CASE WHEN date_limite IS NULL THEN 1 ELSE 0 END")
            ->orderBy('date_limite')
            ->first();

        if ($obligation) {
            $doc->obligation_id = $obligation->id;
            $doc->save();
            if ($obligation->statut === Obligation::STATUT_DECLARE) {
                $obligation->statut = Obligation::STATUT_JUSTIFICATIF;
                $obligation->save();
            }
            $this->suivi->mirrorDeclarationStatut($obligation);
            $this->suivi->syncNotifications();
        }
    }
}
