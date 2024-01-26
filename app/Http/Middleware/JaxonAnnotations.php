<?php

namespace App\Http\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Closure;

use function Jaxon\jaxon;
use function storage_path;

class JaxonAnnotations
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
        jaxon()->di()->val('jaxon_annotations_cache_dir', storage_path('annotations'));

        return $next($request);
    }
}
