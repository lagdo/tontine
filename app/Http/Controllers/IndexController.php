<?php

namespace App\Http\Controllers;

use App\Ajax\Web\Meeting\Session\Session;
use App\Ajax\Web\Tontine\Tontine;
use Illuminate\View\View;
use Jaxon\Laravel\Jaxon;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Service\LocaleService;

use function auth;
use function config;
use function session;
use function view;

class IndexController extends Controller
{
    /**
     * Show the home page.
     *
     * @param Jaxon $jaxon
     *
     * @return View
     */
    public function index(Jaxon $jaxon): View
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();
        // The Jaxon request processing path is not localized. So we need to save the current
        // locale in the session, so we can have it when processing the Jaxon ajax requests.
        session(['jaxonCurrentLocale' => $currentLocale]);

        view()->share([
            'user' => auth()->user(),
            'locales' => LaravelLocalization::getSupportedLocales(),
            'locale' => $currentLocale,
            'localeNative' => LaravelLocalization::getCurrentLocaleNative(),
            'jxnSession' => $jaxon->request(Session::class),
            'jxnTontine' => $jaxon->request(Tontine::class),
        ]);

        $template = config('tontine.templates.app', 'default');
        return view("tontine.app.$template.base.home", [
            'jaxonCss' => $jaxon->css(),
            'jaxonJs' => $jaxon->js(),
            'jaxonScript' => $jaxon->script(),
        ]);
    }

    /**
     * Show the user profile page.
     *
     * @return View
     */
    public function profile(LocaleService $localeService): View
    {
        view()->share([
            'user' => auth()->user(),
            'locales' => LaravelLocalization::getSupportedLocales(),
            'locale' => LaravelLocalization::getCurrentLocale(),
            'localeNative' => LaravelLocalization::getCurrentLocaleNative(),
        ]);

        $template = config('tontine.templates.app', 'default');
        return view("tontine.app.$template.base.profile", [
            'countries' => $localeService->getCountries(),
        ]);
    }
}
