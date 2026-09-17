<?php
/*
|--------------------------------------------------------------------------
| app/Http/Controllers/DocumentController.php
|--------------------------------------------------------------------------
*/

namespace App\Http\Controllers;

use App\Document;
use App\Contribuable;
use App\TrackedDocType;          // ← ajouter
use App\DeclarationStatut; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Page HTML "Documents (GED)" */
    public function page()
    {
        $data = DeclarationController::donneesSuivi();   // contribuables + trackedDocTypes + statuts
        $data['initialDocuments'] = Document::with('contribuable')->actifs()->latest()->get()
                                        ->map(function ($d) { return $d->toFront(); })->values();
 
        return view('documents.index', $data);
    }

    /** Page HTML "Archives" */
    public function archivesPage()
    {
        return view('archives.index', [
            'initialArchives'      => Document::with('contribuable')->archives()->latest('archived_at')->get()
                                        ->map(function ($d) { return $d->toFront(); })->values(),
            'initialContribuables' => Contribuable::latest()->get(),
        ]);
    }

    /** Création d'un document (multipart : champs + fichier éventuel) */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $doc = new Document();
        $doc->fill($this->fields($data));
        $this->attachFile($request, $doc);
        $doc->save();
        $this->autoLierDocumentSuivi($doc);

        return response()->json(['document' => $doc->load('contribuable')->toFront()]);
    }

    /** Modification d'un document ; si aucun fichier n'est envoyé, l'ancien est conservé */
    public function update(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        $data = $this->validated($request);

        $doc->fill($this->fields($data));
        $this->attachFile($request, $doc);   // remplace le fichier seulement si un nouveau est fourni
        $doc->save();
        $this->autoLierDocumentSuivi($doc);

        return response()->json(['document' => $doc->load('contribuable')->toFront()]);
    }

    /** Archiver : on ne supprime rien, on horodate simplement */
    public function archive($id)
    {
        $doc = Document::findOrFail($id);
        $doc->archived_at = now();
        $doc->save();

        return response()->json(['document' => $doc->load('contribuable')->toFront()]);
    }

    /** Restaurer un document archivé */
    public function restore($id)
    {
        $doc = Document::findOrFail($id);
        $doc->archived_at = null;
        $doc->save();

        return response()->json(['document' => $doc->load('contribuable')->toFront()]);
    }

    /** Suppression définitive : la ligne ET le fichier sur le disque */
    public function destroy($id)
    {
        $doc = Document::findOrFail($id);

        if ($doc->fichier_path && Storage::disk('public')->exists($doc->fichier_path)) {
            Storage::disk('public')->delete($doc->fichier_path);
        }
        $doc->delete();

        return response()->json(['success' => true]);
    }

    /** Téléchargement / aperçu du fichier joint */
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

    // ------------------------------------------------------------------

    private function validated(Request $request)
    {
        return $request->validate([
            'nom'             => 'required|string|max:255',
            'type'            => 'nullable|string|max:120',
            'fournisseur'     => 'nullable|string|max:255',
            'montant'         => 'nullable|integer|min:0',
            'contribuable_id' => 'nullable|integer|exists:contribuables,id',
            // 10 Mo max, formats autorisés par le cahier des charges + quelques extras utiles
            'fichier'         => 'nullable|file|max:10240|mimes:pdf,png,jpg,jpeg',
        ]);
    }

    private function fields(array $data)
    {
        return [
            'nom'             => $data['nom'],
            'type'            => isset($data['type']) ? $data['type'] : null,
            'fournisseur'     => isset($data['fournisseur']) ? $data['fournisseur'] : null,
            'montant'         => isset($data['montant']) ? $data['montant'] : 0,
            'contribuable_id' => isset($data['contribuable_id']) ? $data['contribuable_id'] : null,
        ];
    }

    private function attachFile(Request $request, Document $doc)
    {
        if (! $request->hasFile('fichier')) {
            return; // aucun nouveau fichier : on garde celui déjà enregistré
        }

        // on supprime l'ancien fichier pour ne pas laisser de fichiers orphelins
        if ($doc->fichier_path && Storage::disk('public')->exists($doc->fichier_path)) {
            Storage::disk('public')->delete($doc->fichier_path);
        }

        $file = $request->file('fichier');
        $doc->fichier_path = $file->store('documents', 'public');
        $doc->fichier_nom  = $file->getClientOriginalName();
        $doc->fichier_mime = $file->getClientMimeType();
    }
    private function autoLierDocumentSuivi(Document $doc)
    {
        if (! $doc->contribuable_id || ! $doc->type) {
            return;
        }
 
        $type = TrackedDocType::whereRaw('LOWER(TRIM(nom)) = ?', [mb_strtolower(trim($doc->type))])->first();
        if (! $type) {
            return; // ce type de document n'est pas suivi : rien à faire
        }
 
        // firstOrCreate : on ne réinitialise pas un statut déjà saisi par l'utilisateur
        DeclarationStatut::firstOrCreate(
            [
                'contribuable_id'     => $doc->contribuable_id,
                'tracked_doc_type_id' => $type->id,
            ],
            ['statut' => 'non_declare']
        );
    }
}