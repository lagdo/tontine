<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Ajax\App\Tontine\Tontine;
use Jaxon\Laravel\Jaxon;

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
        // Localized Jaxon request processing URI
        $jaxon->setOption('core.request.uri', LaravelLocalization::localizeUrl('/ajax'));

        view()->share([
            'user' => auth()->user(),
            'locales' => LaravelLocalization::getSupportedLocales(),
            'locale' => LaravelLocalization::getCurrentLocale(),
            'localeNative' => LaravelLocalization::getCurrentLocaleNative(),
            'jxnTontine' => $jaxon->request(Tontine::class),
        ]);

        return view('tontine.base.home', [
            'pageTitle' => "Siak Tontine",
            'jaxonCss' => $jaxon->css(),
            'jaxonJs' => $jaxon->js(),
            'jaxonScript' => $jaxon->script(),
        ]);
    }
}
