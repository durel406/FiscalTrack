<?php
/*
|--------------------------------------------------------------------------
| app/Document.php   (à côté de App\User et App\Contribuable)
|--------------------------------------------------------------------------
| ⚠️ Namespace App, PAS App\Models — c'est la structure de votre projet.
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'nom', 'type', 'fournisseur', 'montant', 'contribuable_id', 'obligation_id',
        'fichier_path', 'fichier_nom', 'fichier_mime', 'fichier_url', 'fichier_disk', 'archived_at',
    ];

    protected $dates = ['archived_at'];

    public function contribuable()
    {
        return $this->belongsTo(Contribuable::class);
    }

    public function obligation()
    {
        return $this->belongsTo(Obligation::class);
    }

    /** Documents actifs (non archivés) */
    public function scopeActifs($query)
    {
        return $query->whereNull('archived_at');
    }

    /** Documents archivés */
    public function scopeArchives($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Transforme le document au format attendu par le JavaScript de l'application.
     * C'est ici qu'on fait la correspondance entre les colonnes MySQL (snake_case)
     * et les clés utilisées dans le front (nom, contrib, date, dateModif...).
     */
    public function toFront()
    {
        return [
            'id'             => $this->id,
            'nom'            => $this->nom,
            'type'           => $this->type,
            'contribuable_id'=> $this->contribuable_id,
            'obligation_id'  => $this->obligation_id,
            'contrib'        => $this->contribuable ? $this->contribuable->nom : '—',
            'fournisseur'    => $this->fournisseur ?: '—',
            'montant'        => (int) $this->montant,
            'date'           => $this->created_at ? $this->created_at->format('d/m/Y') : '—',
            'dateModif'      => $this->updated_at ? $this->updated_at->format('d/m/Y') : '—',
            'archivedDate'   => $this->archived_at ? $this->archived_at->format('d/m/Y') : null,
            'fileName'       => $this->fichier_nom,
            'fileUrl'        => $this->fichier_url ?: ($this->fichier_path ? url('/documents/'.$this->id.'/fichier') : null),
            'fileMime'       => $this->fichier_mime,
        ];
    }
}