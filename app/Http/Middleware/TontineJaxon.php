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
        $userDir = [
            'path' => base_path('ajax/User'),
            'namespace' => '\\Ajax\\User',
        ];
        $jaxonRouteName = config('jaxon.app.request.route', 'jaxon.ajax');

        return match(true) {
            // Register all the directories on the Ajax page
            $request->routeIs($jaxonRouteName) => [
                'jaxon.app.directories' => [$pageDir, $appDir, $userDir],
            ],
            $request->routeIs('tontine.home') => [
                'jaxon.app.directories' => [$pageDir, $appDir],
                'jaxon.lib.js.app.file' => env('JAXON_JS_APP_FILE', 'app-4.0.9'),
            ],
            $request->routeIs('user.invites') => [
                'jaxon.app.directories' => [$pageDir, $userDir],
                'jaxon.lib.js.app.file' => env('JAXON_JS_USERS_FILE', 'users-4.0.0'),
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
        $options = $this->getOptions($request);
        count($options) > 0 && config($options);

        return $next($request);
    }
}
