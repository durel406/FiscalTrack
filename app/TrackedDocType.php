<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackedDocType extends Model
{
    protected $table = 'tracked_doc_types';

    protected $fillable = ['nom', 'date_limite', 'periodicite', 'organisme_defaut'];

    protected $dates = ['date_limite'];

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
            'date_limite'      => $this->date_limite ? $this->date_limite->format('Y-m-d') : null,
            'periodicite'      => $this->periodicite ?: 'libre',
            'organisme_defaut' => $this->organisme_defaut,
        ];
    }
}
