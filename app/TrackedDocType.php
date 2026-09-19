<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackedDocType extends Model
{
    protected $table = 'tracked_doc_types';

    protected $fillable = ['nom', 'periodicite', 'organisme_defaut'];

    public function statuts()
    {
        return $this->hasMany(DeclarationStatut::class);
    }

    public function obligations()
    {
        return $this->hasMany(Obligation::class);
    }

    public function toFront()
    {
        return [
            'id'               => $this->id,
            'nom'              => $this->nom,
            'periodicite'      => $this->periodicite ?: 'libre',
            'organisme_defaut' => $this->organisme_defaut,
        ];
    }
}
