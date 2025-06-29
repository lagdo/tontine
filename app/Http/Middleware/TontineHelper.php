<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use Siak\Tontine\Service\LocaleService;

use function app;
use function html;
use function jaxon;

class TontineHelper
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
        View::share('locale', app()->make(LocaleService::class));
        View::share('html', html());
        View::share('stash', jaxon()->di()->getStash());

        return $next($request);
    }
}
