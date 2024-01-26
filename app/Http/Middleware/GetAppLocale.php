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

class GetAppLocale
{
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
        // Try to get the current locale from the session.
        $locale = session('jaxonCurrentLocale', LaravelLocalization::getCurrentLocale());
        LaravelLocalization::setLocale($locale);

        // Same as for SetAppLocale, excepted that the locale is read in the session.
        Carbon::setLocale($locale);
        setlocale(LC_TIME, $locale);
        // For comparison of strings with accented characters in french
        setlocale(LC_COLLATE, LaravelLocalization::getCurrentLocaleRegional() . '.utf8');

        return $next($request);
    }
}
