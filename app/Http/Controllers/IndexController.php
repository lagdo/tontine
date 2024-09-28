<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Jaxon\Laravel\App\Jaxon;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Service\LocaleService;

use function auth;
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
        view()->share([
            'user' => auth()->user(),
            'locales' => LaravelLocalization::getSupportedLocales(),
            'locale' => LaravelLocalization::getCurrentLocale(),
            'localeNative' => LaravelLocalization::getCurrentLocaleNative(),
        ]);

        return view("tontine::base.home");
    }

    /**
     * Show the user profile page.
     *
     * @param LocaleService $localeService
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

        return view("tontine::base.profile", [
            'countries' => $localeService->getCountries(),
        ]);
    }
}
