<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
        // $authUser est déjà injecté automatiquement par le View::composer (voir AppServiceProvider).
        // Les KPI/timeline/calendrier sont pour l'instant calculés côté JS à partir des données
        // en mémoire (contribuables, trackedDocTypes...) — voir la remarque de fin de réponse
        // sur le rechargement de ces données depuis MySQL.
    }
}
