<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Model\User;
use Closure;

use function auth;
use function setlocale;

class TontineLocale
{
    /**
     * @param Request $request
     *
     * @return string
     */
    private function getLocale(Request $request): string
    {
        $locale = LaravelLocalization::getCurrentLocale();
        /** @var User */
        $user = auth()->user();
        // The Jaxon request processing path is not localized. So we need to save the current
        // locale in the database, so we can have it when processing the Jaxon ajax requests.
        if($request->routeIs('tontine.home'))
        {
            // On the home page, save the current locale in the database.
            $properties = $user->properties;
            if($locale !== ($properties['locale'] ?? ''))
            {
                $properties['locale'] = $locale;
                $user->saveProperties($properties);
            }
        }
        elseif($request->routeIs('jaxon'))
        {
            // For Jaxon requests, try to get the current locale from the database.
            $locale = $user->properties['locale'] ?? LaravelLocalization::getCurrentLocale();
            LaravelLocalization::setLocale($locale);
        }

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
