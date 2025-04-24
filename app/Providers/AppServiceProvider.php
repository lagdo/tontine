<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        $url = $this->app['url'];
        if($this->app->environment('production'))
        {
            $url->forceScheme('https');
        }
    }
}
