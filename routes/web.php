<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login')->middleware('guest');
Route::post('/login', 'Auth\LoginController@login')->middleware('guest');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $users = \App\User::orderBy('name')->get()->map(function ($user) {
            $roleMap = [
                'admin' => 'Administrateur',
                'comptable' => 'Comptable',
                'responsable_fiscal' => 'Responsable fiscal',
                'fiscal' => 'Responsable fiscal',
            ];
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

        return view('home', [
            'authUser' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role === 'responsable_fiscal' ? 'fiscal' : auth()->user()->role,
                'role_label' => [
                    'admin' => 'Administrateur',
                    'comptable' => 'Comptable',
                    'fiscal' => 'Responsable fiscal',
                    'responsable_fiscal' => 'Responsable fiscal',
                ][auth()->user()->role] ?? auth()->user()->role,
            ],
            'initialUsers' => $users,
        ]);
    })->name('dashboard');

    Route::get('/users', 'UserController@index')->name('users.index');
    Route::post('/users', 'UserController@store')->name('users.store');
    Route::put('/users/{id}', 'UserController@update')->name('users.update');
    Route::patch('/users/{id}/toggle-status', 'UserController@toggleStatus')->name('users.toggle');
    Route::delete('/users/{id}', 'UserController@destroy')->name('users.destroy');
});
