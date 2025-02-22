<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jaxon\Laravel\App\Jaxon;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\User;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\TontineService;

use function auth;
use function Jaxon\jaxon;
use function view;

class TontineTenant
{
    /**
     * @param Jaxon $jaxon
     * @param TenantService $tenantService
     * @param TontineService $tontineService
     */
    public function __construct(private Jaxon $jaxon, private TenantService $tenantService,
        private TontineService $tontineService)
    {}

    /**
     * Get the latest user tontine, from the session or the database.
     *
     * @param User $user
     *
     * @return Tontine|null
     */
    private function setLatestTontine(User $user): ?Tontine
    {
        $tenantDatabag = jaxon()->getResponse()->bag('tenant');

        // First try to get the current tontine id from the databag.
        $tontine = null;
        $tontineId = $tenantDatabag->get('tontine.id', 0);
        if($tontineId > 0 &&
            ($tontine = $this->tontineService->getUserOrGuestTontine($tontineId)) !== null)
        {
            $this->tenantService->setTontine($tontine);
            return $tontine;
        }

        // Try to get the latest tontine the user worked on.
        if(($tontineId = $user->properties['latest']['tontine'] ?? 0) > 0)
        {
            $tontine = $this->tontineService->getUserOrGuestTontine($tontineId);
        }
        if(!$tontine)
        {
            $tontine = $this->tontineService->getFirstTontine();
        }
        if($tontine !== null)
        {
            $tenantDatabag->set('tontine.id', $tontine->id);
            $tenantDatabag->set('round.id', 0);
            $this->tenantService->setTontine($tontine);
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
    private function setLatestRound(Tontine $tontine): ?Round
    {
        $tenantDatabag = jaxon()->getResponse()->bag('tenant');

        // First try to get the current round id from the databag.
        $round = null;
        $roundId = $tenantDatabag->get('round.id', 0);
        if($roundId > 0 &&
            ($round = $this->tenantService->getRound($roundId)) !== null)
        {
            $this->tenantService->setRound($round);
            return $round;
        }

        // Try to get the latest round the user worked on.
        if(($roundId = $tontine->user->properties['latest']['round'] ?? 0) > 0)
        {
            $round = $this->tenantService->getRound($roundId);
        }
        if(!$round)
        {
            $round = $this->tenantService->getFirstRound();
        }
        if($round !== null)
        {
            $this->tenantService->setRound($round);
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

        if(($tontine = $this->setLatestTontine($user)) !== null)
        {
            $this->setLatestRound($tontine);
        }
        view()->share('tontine', $tontine);

        return $next($request);
    }
}
