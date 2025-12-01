<?php

namespace App\Providers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Browser;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Uri;
use Lagdo\Facades\Logger;
use Siak\Tontine\Exception\PdfGeneratorException;
use Siak\Tontine\Service\Report\Pdf\LocalPdfGenerator;
use Siak\Tontine\Service\Report\Pdf\PdfGeneratorInterface;
use Exception;

use function config;

class SiakExtServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @param array $options
     *
     * @throws PdfGeneratorException
     * @return Browser|null
     */
    private function connectToChrome(array $options): Browser|null
    {
        $chromeHost = config('chrome.host');
        $chromePort = config('chrome.port');
        if($chromeHost === null || $chromePort === null)
        {
            return null;
        }

        $chromeHttpUrl = "http://$chromeHost:$chromePort/json/version";
        try
        {
            $chromeVersion = Http::accept('application/json')->get($chromeHttpUrl);
            $chomeWsUrl = Uri::of($chromeVersion['webSocketDebuggerUrl'] ?? '');
            // Only take the path because due to the Nginx proxy, the full uri might not be correct.
            $chomeWsUrl = "ws://$chromeHost:$chromePort/" . $chomeWsUrl->path();
            if(config('app.debug'))
            {
                Logger::info("Connect to the headless Chrome URI at '$chomeWsUrl'");
            }
            return BrowserFactory::connectToBrowser($chomeWsUrl, $options);
        }
        catch(Exception)
        {
            Logger::error("Unable to connect to the headless Chrome URI at '$chromeHttpUrl'");
            throw new PdfGeneratorException("Unable to connect to the headless Chrome URI");
        }
    }

    /**
     * @param array $options
     *
     * @throws PdfGeneratorException
     * @return Browser
     */
    private function startChromeBinary(array $options): Browser
    {
        // Try to launch the headless chrome binary.
        $chromeBinary = config('chrome.binary');
        try
        {
            $browserFactory = new BrowserFactory($chromeBinary);
            return $browserFactory->createBrowser($options);
        }
        catch(Exception)
        {
            Logger::error("Unable to launch the headless Chrome binary at '$chromeBinary'");
            throw new PdfGeneratorException("Unable to launch the headless Chrome binary");
        }
    }

    /**
     * Register any application services
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Browser::class, function() {
            $options = config('chrome.browser', []);
            if(config('app.debug'))
            {
                $options['debugLogger'] = Logger::instance();
            }

            return $this->connectToChrome($options) ?? $this->startChromeBinary($options);
        });

        $this->app->bind(PdfGeneratorInterface::class, LocalPdfGenerator::class);
        $this->app->singleton(LocalPdfGenerator::class, fn($app) =>
            new LocalPdfGenerator($app->make(Browser::class)));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [PdfGeneratorInterface::class];
    }
}
