<?php

namespace App\Http\Middleware;

use App\Ajax\CallableClass;
use App\Ajax\Component;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jaxon\Laravel\App\Jaxon;
use Closure;

use function app;
use function is_a;

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
        $jaxon->callback()->init(function($callable) use($jaxon) {
            if(!is_a($callable, Component::class) && !is_a($callable, CallableClass::class))
            {
                return;
            }

            // Jaxon init
            $dialog = $jaxon->ajaxResponse()->dialog;
            $callable->dialog = $dialog;
            $callable->notify = $dialog;
        });

        return $next($request);
    }
}
