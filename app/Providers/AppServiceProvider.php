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
                'responsable_fiscal' => 'Responsable fiscal',
            ];

            $roleKey = $user && $user->role === 'responsable_fiscal' ? 'fiscal' : ($user ? $user->role : null);

            $view->with('authUser', $user ? [
                'name'       => $user->name,
                'role'       => $roleKey,
                'role_label' => $roleLabels[$user->role] ?? $user->role,
            ] : null);

            if ($user) {
                $data = $view->getData();
                if (! isset($data['initialNotifications'])) {
                    try {
                        $suivi = app(\App\Services\FiscalSuiviService::class);
                        $view->with('initialNotifications', $suivi->listNotificationsForFront());
                        if (! isset($data['suiviKpis'])) {
                            $view->with('suiviKpis', $suivi->kpis());
                        }
                        if (! isset($data['initialObligations'])) {
                            $view->with('initialObligations', $suivi->listObligationsForFront());
                        }
                    } catch (\Throwable $e) {
                        $view->with('initialNotifications', collect());
                        $view->with('suiviKpis', null);
                        $view->with('initialObligations', collect());
                    }
                }
            }
        });
    }
}
