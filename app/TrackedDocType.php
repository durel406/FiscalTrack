<?php

namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class TrackedDocType extends Model
{
    protected $table = 'tracked_doc_types';
 
    protected $fillable = ['nom', 'date_limite'];
 
    protected $dates = ['date_limite'];
 
    public function statuts()
    {
        return $this->hasMany(DeclarationStatut::class);
    }
 
    /** Format attendu par le JavaScript de l'application */
    public function toFront()
    {
        return [
            'id'          => $this->id,
            'nom'         => $this->nom,
            'date_limite' => $this->date_limite ? $this->date_limite->format('Y-m-d') : null,
        ];
    }
}