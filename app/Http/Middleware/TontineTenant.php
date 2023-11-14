<?php

namespace App\Http\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\User;
use Siak\Tontine\Service\TenantService;
use Closure;

use function auth;
use function session;
use function view;

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

    /**
     * Get the latest user tontine, from the session or the database.
     *
     * @param User $user
     *
     * @return Tontine|null
     */
    private function getLatestTontine(User $user): ?Tontine
    {
        $tontineId = session('tontine.id', 0);
        if($tontineId > 0 && ($tontine = $user->tontines()->find($tontineId)) !== null)
        {
            return $tontine;
        }
        $tontineId = $user->properties['latest']['tontine'] ?? 0;
        if($tontineId > 0 && ($tontine = $user->tontines()->find($tontineId)) !== null)
        {
            return $tontine;
        }
        return $user->tontines()->first();
    }

    /**
     * Get the latest tontine round, from the session or the database.
     *
     * @param Tontine $tontine
     *
     * @return Round|null
     */
    private function getLatestRound(Tontine $tontine): ?Round
    {
        $roundId = session('round.id', 0);
        if($roundId > 0 && ($round = $tontine->rounds()->find($roundId)) !== null)
        {
            return $round;
        }
        $roundId = $tontine->user->properties['latest']['round'] ?? 0;
        if($roundId > 0 && ($round = $tontine->rounds()->find($roundId)) !== null)
        {
            return $round;
        }
        return $tontine->rounds()->first();
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
        /** @var User */
        $user = auth()->user();
        $this->tenantService->setUser($user);

        $tontineId = 0;
        $roundId = 0;
        if(($tontine = $this->getLatestTontine($user)) !== null)
        {
            $tontineId = $tontine->id;
            $this->tenantService->setTontine($tontine);
            if(($round = $this->getLatestRound($tontine)) !== null)
            {
                $roundId = $round->id;
                $this->tenantService->setRound($round);
            }
        }
        view()->share('tontine', $tontine);
        session(['tontine.id' => $tontineId, 'round.id' => $roundId]);

        return $next($request);
    }
}
