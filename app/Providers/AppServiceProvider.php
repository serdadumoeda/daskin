<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate; 
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Tambahkan ini: Gate akan selalu return true jika user memiliki role 'superadmin'
        // Ini efektif memberikan semua izin tanpa harus menetapkannya secara manual.
        Gate::before(function ($user, $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });
    }
}