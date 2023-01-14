<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Siak\Tontine\Service\Tontine\TenantService;
use Closure;

use function auth;
use function session;

class TontineTenant
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    private function initTontine()
    {
        $user = auth()->user();
        $this->tenantService->setUser($user);
        $tontine = $user->tontines()->find(session('tontine.id', 0));
        if(!$tontine)
        {
            session(['tontine.id' => 0, 'round.id' => 0]);
            return;
        }

        $this->tenantService->setTontine($tontine);
        $round = $tontine->rounds()->find(session('round.id', 0));
        if(!$round)
        {
            session(['round.id' => 0]);
            return;
        }
        $this->tenantService->setRound($round);
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->initTontine();

        return $next($request);
    }
}
