<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contribuable extends Model
{
    protected $fillable = [
        'nom',
        'niu',
        'regime',
        'cat',
        'statut',
        'pass',
        'montant',
        't1',
        't2',
        't3',
        't4',
        'tdl',
        'impots',
        'loyer',
        'bail',
        'precompte',
        'timbre',
        'frais_paiement',
        'fs_paye',
        'fs_non_paye',
        'ai_igs',
        'ai_bail',
        'ai_precompte',
        'q_igs',
        'q_bail',
        'q_precompte',
        'acf_igs',
        'acf_bail',
        'acf_precompte',
        'lieu',
        'tel',
    ];

    /**
     * Chiffre le mot de passe avant son enregistrement.
     */
    public function setPassAttribute($value)
    {
        if ($value) {
            $this->attributes['pass'] = encrypt($value);
        } else {
            $this->attributes['pass'] = null;
        }
    }

    /**
     * Déchiffre le mot de passe lors de sa lecture.
     */
    public function getPassAttribute($value)
    {
        if ($value) {
            return decrypt($value);
        }

        return null;
    }
}