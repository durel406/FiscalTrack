<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DossierSuivi;

class DossierSuiviController extends Controller
{
    public function store(Request $request)
    {
        // Vérifier les données reçues
        $request->validate([
            'nom_entreprise' => 'required|string|max:255',
            'annee' => 'required|integer',
        ]);

        // Enregistrer dans MySQL
        $dossier = DossierSuivi::create([
            'nom_entreprise' => $request->nom_entreprise,
            'annee' => $request->annee,
        ]);

        // Réponse envoyée à JavaScript
        return response()->json([
            'success' => true,
            'message' => 'Dossier enregistré avec succès.',
            'dossier' => $dossier
        ]);
    }
}