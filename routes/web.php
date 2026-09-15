<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DossierSuiviController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login')->middleware('guest');
Route::post('/login', 'Auth\LoginController@login')->middleware('guest');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout')->middleware('auth');

Route::post('/contribuables', 'ContribuableController@store')->name('contribuables.store');
Route::put('/contribuables/{id}', 'ContribuableController@update')->name('contribuables.update');
Route::delete('/contribuables/{id}', 'ContribuableController@destroy')->name('contribuables.destroy');
Route::post('/dossier-suivi/enregistrer', [DossierSuiviController::class, 'store'])
    ->name('dossier-suivi.store');
/*
|--------------------------------------------------------------------------
| $authUser : partagé par toutes les pages protégées (utilisé dans le layout)
|--------------------------------------------------------------------------
| C'est exactement le tableau que vous construisiez déjà dans l'ancienne
| route /dashboard, extrait ici pour ne pas le dupliquer sur les 7 écrans.
*/
function fiscaltrackAuthUser(): array
{
    $roleLabels = [
        'admin' => 'Administrateur',
        'comptable' => 'Comptable',
        'fiscal' => 'Responsable fiscal',
        'responsable_fiscal' => 'Responsable fiscal',
    ];
    $user = auth()->user();
    $roleKey = $user->role === 'responsable_fiscal' ? 'fiscal' : $user->role;

    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $roleKey,
        'role_label' => $roleLabels[$user->role] ?? $user->role,
    ];
}

/*
|--------------------------------------------------------------------------
| $initialUsers : uniquement nécessaire sur l'écran Comptes utilisateurs
|--------------------------------------------------------------------------
*/
function fiscaltrackInitialUsers()
{
    $roleMap = [
        'admin' => 'Administrateur',
        'comptable' => 'Comptable',
        'responsable_fiscal' => 'Responsable fiscal',
        'fiscal' => 'Responsable fiscal',
    ];

    return \App\User::orderBy('name')->get()->map(function ($user) use ($roleMap) {
        $roleKey = $user->role === 'responsable_fiscal' ? 'fiscal' : $user->role;

        return [
            'id' => $user->id,
            'nom' => $user->name,
            'email' => $user->email,
            'role' => $roleMap[$user->role] ?? $user->role,
            'role_key' => $roleKey,
            'statut' => $user->status ?: 'active',
        ];
    })->values();
}

Route::middleware('auth')->group(function () {

    // ---- Tableau de bord ----
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'authUser' => fiscaltrackAuthUser(),
            'initialContribuables' => \App\Contribuable::latest()->get()
        ]);
    })->name('dashboard');

    // ---- Contribuables ----
    Route::get('/contribuables', function () {
        return view('contribuables.index', [
            'authUser' => fiscaltrackAuthUser(),
            'initialContribuables' => \App\Contribuable::latest()->get(), // à remplacer par \App\Contribuable::latest()->get() une fois le modèle créé
        ]);
    })->name('contribuables.index');

    // ---- Documents (GED) ----
    Route::get('/documents', function () {
        return view('documents.index', [
            'authUser' => fiscaltrackAuthUser(),
            'initialContribuables' => \App\Contribuable::latest()->get(),
        ]);
    })->name('documents.index');

    // ---- Archives ----
    Route::get('/archives', function () {
        return view('archives.index', [
            'authUser' => fiscaltrackAuthUser(),
        ]);
    })->name('archives.index');

    // ---- Déclaration ----
    Route::get('/declarations', function () {
        return view('declarations.index', [
            'authUser' => fiscaltrackAuthUser(),
        ]);
    })->name('declarations.index');

    // ---- Notifications ----
    Route::get('/notifications', function () {
        return view('notifications.index', [
            'authUser' => fiscaltrackAuthUser(),
        ]);
    })->name('notifications.index');

    // ---- Comptes utilisateurs (page HTML) ----
    Route::get('/comptes', function () {
        return view('comptes.index', [
            'authUser' => fiscaltrackAuthUser(),
            'initialUsers' => fiscaltrackInitialUsers(),
        ]);
    })->name('comptes.index');

    // ---- API JSON Comptes utilisateurs (déjà existante, inchangée) ----
    // Utilisée par apiUsers()/reloadUsers() côté JS pour créer/modifier/lister sans recharger la page.
    Route::get('/users', 'UserController@page')->name('users.index');
    Route::post('/users', 'UserController@store')->name('users.store');
    Route::put('/users/{id}', 'UserController@update')->name('users.update');
    Route::patch('/users/{id}/toggle-status', 'UserController@toggleStatus')->name('users.toggle');
    Route::delete('/users/{id}', 'UserController@destroy')->name('users.destroy');
});
