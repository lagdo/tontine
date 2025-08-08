<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Siak\Tontine\Model\User;

use function env;
use function explode;
use function in_array;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Log database queries
        // DB::listen(function($query) {
        //     Log::info($query->sql, $query->bindings, $query->time);
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // app()->usePublicPath(__DIR__.'/public');

        Schema::defaultStringLength(190);

        // Force redirect to HTTPS.
        if($this->app->environment('production'))
        {
            $this->app['url']->forceScheme('https');
        }

        // Access on analytics pages
        Gate::define('analytics', function(User $user) {
            return in_array($user->email, explode(',', env('ANALYTICS_USERS', '')));
        });
    }
}
