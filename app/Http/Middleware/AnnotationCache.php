<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

use function jaxon;
use function storage_path;

class AnnotationCache
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        jaxon()->di()->val('jaxon_annotations_cache_dir', storage_path('annotations'));

        return $next($request);
    }
}
