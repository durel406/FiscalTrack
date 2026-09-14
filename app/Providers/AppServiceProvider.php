<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();
    
            $roleLabels = [
                'admin'     => 'Administrateur',
                'comptable' => 'Comptable',
                'fiscal'    => 'Responsable fiscal',
            ];
    
            $view->with('authUser', $user ? [
                'name'       => $user->name,
                'role'       => $user->role,                              // 'admin' | 'comptable' | 'fiscal'
                'role_label' => $roleLabels[$user->role] ?? $user->role,
            ] : null);
        });
    }
}
