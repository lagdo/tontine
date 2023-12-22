<?php

namespace App\Providers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Browser;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Siak\Tontine\Service\Report\Pdf\GeneratorInterface;
use Siak\Tontine\Service\Report\Pdf\LocalGenerator;

use function config;

class SiakPdfServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Browser::class, function() {
            $browserFactory = new BrowserFactory(config('chrome.binary'));
            // Starts headless chrome
            return $browserFactory->createBrowser(config('chrome.browser', []));
        });

        $this->app->bind(GeneratorInterface::class, LocalGenerator::class);
        $this->app->singleton(LocalGenerator::class, function($app) {
            return new LocalGenerator($app->make(Browser::class));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [GeneratorInterface::class];
    }
}
