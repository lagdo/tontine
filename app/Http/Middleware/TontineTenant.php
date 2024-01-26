<?php

namespace App\Http\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jaxon\Laravel\Jaxon;
use Jaxon\Plugin\Response\DataBag\DataBagContext;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\User;
use Siak\Tontine\Service\TenantService;
use Closure;

use function auth;
use function Jaxon\jaxon;
use function view;

class TontineTenant
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(private Jaxon $jaxon,
        private TenantService $tenantService)
    {}

    /**
     * Get the latest user tontine, from the session or the database.
     *
     * @param User $user
     *
     * @return Tontine|null
     */
    private function getLatestTontine(User $user): ?Tontine
    {
        /** @var DataBagContext */
        $tenantDatabag = jaxon()->getResponse()->bag('tenant');

        // First try to get the tontine from the databag.
        $tontineId = $tenantDatabag->get('tontine.id', 0);
        if($tontineId > 0 &&
            ($tontine = $user->tontines()->find($tontineId)) !== null)
        {
            return $tontine;
        }

        $tontineId = $user->properties['latest']['tontine'] ?? 0;
        if($tontineId > 0)
        {
            $tontine = $user->tontines()->find($tontineId);
        }
        if(!$tontine)
        {
            $tontine = $user->tontines()->first();
        }
        if(($tontine))
        {
            $tenantDatabag->set('tontine.id', $tontine->id);
            $tenantDatabag->set('round.id', 0);
        }
        return $tontine;
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
        /** @var DataBagContext */
        $tenantDatabag = jaxon()->getResponse()->bag('tenant');

        // First try to get the round from the databag.
        $roundId = $tenantDatabag->get('round.id', 0);
        if($roundId > 0 &&
            ($round = $tontine->rounds()->find($roundId)) !== null)
        {
            return $round;
        }

        $roundId = $tontine->user->properties['latest']['round'] ?? 0;
        if($roundId > 0)
        {
            $round = $tontine->rounds()->find($roundId);
        }
        if(!$round)
        {
            $round = $tontine->rounds()->first();
        }
        if(($roundId))
        {
            $tenantDatabag->set('tontine.id', $tontine->id);
            $tenantDatabag->set('round.id', $round->id);
        }
        return $round;
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

        if(($tontine = $this->getLatestTontine($user)) !== null)
        {
            $this->tenantService->setTontine($tontine);
            if(($round = $this->getLatestRound($tontine)) !== null)
            {
                $this->tenantService->setRound($round);
            }
        }
        view()->share('tontine', $tontine);

        return $next($request);
    }
}
