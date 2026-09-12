<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// ouvre la page principale de l'application
 Route::get('/', function () {
     return view('welcome');
 })->name('welcome');
// Route::get('/dashboard', function () {
//     return view('home');
// })->name('dashboard');

// ouvre la page de connexion
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// definie la page d'ouverture apres la connexion
// Route::post('/login', function () {
//     return redirect()->route('dashboard');
// })->name('login.submit');

Route::get('/dashboard', function () {
    return view('home');
})->name('dashboard');