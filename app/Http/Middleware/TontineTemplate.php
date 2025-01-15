<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

use function config;
use function Jaxon\jaxon;
use function resource_path;

class TontineTemplate
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
        $template = config('tontine.templates.app', 'default');
        $tontinePath = resource_path("views/tontine/app/$template");
        $paginationPath = "$tontinePath/parts/table/pagination";
        View::addNamespace('tontine', $tontinePath);

        // Register the namespaces in the Jaxon view renderer.
        $jaxonView = jaxon()->view();
        $jaxonView->addNamespace('tontine', $tontinePath, '.blade.php', 'blade');
        $jaxonView->addNamespace('pagination', $paginationPath, '.blade.php', 'blade');

        return $next($request);
    }
}