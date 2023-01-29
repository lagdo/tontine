<?php

namespace App\Providers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Browser;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Siak\Tontine\Service\Report\PdfGeneratorInterface;
use Siak\Tontine\Service\Report\LocalPdfGenerator;
use Siak\Tontine\Service\Report\ReportServiceInterface;
use Siak\Tontine\Service\Report\ReportService;

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

        $this->app->bind(PdfGeneratorInterface::class, LocalPdfGenerator::class);
        $this->app->singleton(LocalPdfGenerator::class, function($app) {
            return new LocalPdfGenerator($app->make(Browser::class), config('chrome.page'));
        });
        $this->app->singleton(ReportService::class, ReportService::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ReportServiceInterface::class, PdfGeneratorInterface::class];
    }
}
