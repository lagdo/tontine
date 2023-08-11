<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Closure;

use function setlocale;

class SetAppLocale
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
        $locale = LaravelLocalization::getCurrentLocale();
        Carbon::setLocale($locale);
        setlocale(LC_TIME, $locale);
        // For comparison of strings with accented characters in french
        setlocale(LC_COLLATE, LaravelLocalization::getCurrentLocaleRegional() . '.utf8');

        return $next($request);
    }
}
