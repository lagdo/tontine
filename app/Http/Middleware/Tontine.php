<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Siak\Tontine\Model\User;
use Siak\Tontine\Service\TenantService;
use Closure;

use function session;

class Tontine
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

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = User::first();
        $this->tenantService->setUser($user);
        $tontine = $user->tontines()->find(session('tontine.id', 0));
        if(!$tontine)
        {
            $tontine = $user->tontines()->has('rounds')->first();
        }
        if(($tontine))
        {
            $this->tenantService->setTontine($tontine);
            $round = $tontine->rounds()->find(session('round.id', 0));
            if(!$round)
            {
                $round = $tontine->rounds()->first();
            }
            if(($round))
            {
                $this->tenantService->setRound($round);
            }
        }

        return $next($request);
    }
}
