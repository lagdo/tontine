<?php

namespace App\Http\Middleware;

use App\Ajax\CallableClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jaxon\Laravel\Jaxon;
use Closure;

use function app;

class JaxonCallbacks
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
        /** @var Jaxon */
        $jaxon = app()->make(Jaxon::class);
        $jaxon->callback()->init(function(CallableClass $callable) use($jaxon) {
            // Jaxon init
            $dialog = $jaxon->ajaxResponse()->dialog;
            $callable->dialog = $dialog;
            $callable->notify = $dialog;
        });

        return $next($request);
    }
}
