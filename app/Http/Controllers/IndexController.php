<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\LocaleService;

use function auth;
use function view;

class IndexController extends Controller
{
    /**
     * Show the home page.
     *
     * @param GuildService $guildService
     *
     * @return View
     */
    public function index(GuildService $guildService): View
    {
        $user = auth()->user();
        view()->share([
            'user' => $user,
            'locales' => LaravelLocalization::getSupportedLocales(),
            'locale' => LaravelLocalization::getCurrentLocale(),
            'localeNative' => LaravelLocalization::getCurrentLocaleNative(),
            'hasGuestGuilds' => $guildService->hasGuestGuilds($user)
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

    /**
     * Show the users page.
     *
     * @param LocaleService $localeService
     *
     * @return View
     */
    public function users(LocaleService $localeService): View
    {
        view()->share([
            'user' => auth()->user(),
            'locales' => LaravelLocalization::getSupportedLocales(),
            'locale' => LaravelLocalization::getCurrentLocale(),
            'localeNative' => LaravelLocalization::getCurrentLocaleNative(),
        ]);

        return view("tontine::base.users", [
            'countries' => $localeService->getCountries(),
        ]);
    }
}
