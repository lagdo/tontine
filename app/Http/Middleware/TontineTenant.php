<?php

namespace App\Http\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Siak\Tontine\Service\TenantService;
use Closure;

use function auth;
use function view;

class TontineTenant
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(private TenantService $tenantService)
    {}

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
        $this->tenantService->setUser(auth()->user());

        view()->share([
            'currentGuild' => null,
            'currentRound' => null,
        ]);

        return $next($request);
    }
}
