<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
