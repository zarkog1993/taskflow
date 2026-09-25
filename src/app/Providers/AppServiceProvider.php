<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use App\Models\MatchDay;
use App\Models\Permission;
use App\Models\TrainingSession;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kratki aliasi za polimorfne pozivnice (event_invitations.invitable_type i RSVP linkovi)
        Relation::morphMap([
            'training' => TrainingSession::class,
            'match' => MatchDay::class,
        ]);

        // 1. Dinamička registracija permisija za Gate
        try {
            Permission::all()->each(function (Permission $permission) {
                Gate::define($permission->slug, function (User $user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            });
        } catch (\Throwable $e) {
            // Hvatamo izuzetak ako migracije još nisu pokrenute
        }
    }
}
