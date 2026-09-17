<?php

namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class DeclarationStatut extends Model
{
    protected $table = 'declaration_statuts';
 
    protected $fillable = ['contribuable_id', 'tracked_doc_type_id', 'statut'];
 
    public function contribuable()
    {
        return $this->belongsTo(Contribuable::class);
    }
 
    public function trackedDocType()
    {
        return $this->belongsTo(TrackedDocType::class);
    }
}