<?php

namespace App\Http\Controllers;

use App\Ajax\Web\Meeting\Session\Misc;
use App\Ajax\Web\Tontine\Guest\Invite;
use App\Ajax\Web\Tontine\Tontine;
use Illuminate\View\View;
use Jaxon\Laravel\Jaxon;
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
            'jxnSessionMisc' => $jaxon->request(Misc::class),
            'jxnTontine' => $jaxon->request(Tontine::class),
            'jxnInvite' => $jaxon->request(Invite::class),
        ]);

        return view("tontine::base.home", [
            'jaxonCss' => $jaxon->css(),
            'jaxonJs' => $jaxon->js(),
            'jaxonScript' => $jaxon->script(),
        ]);
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
