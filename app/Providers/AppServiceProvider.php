<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Asset;
use App\Observers\AssetObserver;

use Illuminate\Validation\Rules\Password;

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
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        if (!app()->runningInConsole()) {
            try {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => 'create_all_divisions',
                    'guard_name' => 'web'
                ]);
            } catch (\Exception $e) {
            }
        }

        Asset::observe(AssetObserver::class);
    }
}
