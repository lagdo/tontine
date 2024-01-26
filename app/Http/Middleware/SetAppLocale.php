<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Closure;

use function session;
use function setlocale;

class SetAppLocale
{
    /**
     * @param Request $request
     *
     * @return string
     */
    private function getLocale(Request $request): string
    {
        // The Jaxon request processing path is not localized. So we need to save the current
        // locale in the session, so we can have it when processing the Jaxon ajax requests.
        if(!$request->routeIs('jaxon'))
        {
            $locale = LaravelLocalization::getCurrentLocale();
            session(['jaxonCurrentLocale' => $locale]);
            return $locale;
        }

        // For Jaxon requests, try to get the current locale from the session.
        $locale = session('jaxonCurrentLocale', LaravelLocalization::getCurrentLocale());
        LaravelLocalization::setLocale($locale);
        return $locale;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->getLocale($request);

        // Set the locale for date and time.
        Carbon::setLocale($locale);
        setlocale(LC_TIME, $locale);
        // For comparison of strings with accented characters in french
        setlocale(LC_COLLATE, LaravelLocalization::getCurrentLocaleRegional() . '.utf8');

        return $next($request);
    }
}
