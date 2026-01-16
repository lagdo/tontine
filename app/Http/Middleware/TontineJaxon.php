<?php

namespace App\Http\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Closure;

use function base_path;
use function config;
use function count;
use function env;
use function jaxon;

class TontineJaxon
{
    /**
     * @param Request $request
     *
     * @return array
     */
    private function getOptions(Request $request): array
    {
        $appDir = [
            'path' => base_path('ajax/App'),
            'namespace' => '\\Ajax\\App',
        ];
        $pageDir = [
            'path' => base_path('ajax/Page'),
            'namespace' => '\\Ajax\\Page',
        ];
        $jaxonRouteName = config('jaxon.app.request.route', 'jaxon.ajax');

        return match(true) {
            // Register all the directories on the Ajax page
            $request->routeIs($jaxonRouteName) => [
                'jaxon.app.directories' => [$pageDir, $appDir],
            ],
            $request->routeIs('tontine.home') => [
                'jaxon.app.directories' => [$pageDir, $appDir],
                'jaxon.app.assets.file' => env('TONTINE_ASSETS_FILE', 'app-4.0.10-beta.2'),
            ],
            $request->routeIs('user.profile') => [
                'jaxon.app.directories' => [$pageDir],
            ],
            // For the other pages
            default => [], // No Jaxon here
        };
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
        if((count($options = $this->getOptions($request))) > 0)
        {
            config($options);
        }

        jaxon()->config()->globals();

        return $next($request);
    }
}
