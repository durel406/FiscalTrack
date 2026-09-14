<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DossierSuivi extends Model
{
    protected $table = 'dossier_suivi';
    protected $fillable = [
        'nom_entreprise',
        'annee',
    ];
}
