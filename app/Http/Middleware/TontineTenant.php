<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jaxon\Laravel\App\Jaxon;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\User;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\TenantService;

use function auth;
use function Jaxon\jaxon;
use function view;

class TontineTenant
{
    /**
     * @param Jaxon $jaxon
     * @param TenantService $tenantService
     * @param GuildService $guildService
     */
    public function __construct(private Jaxon $jaxon, private TenantService $tenantService,
        private GuildService $guildService)
    {}

    /**
     * Get the latest user guild, from the session or the database.
     *
     * @param User $user
     *
     * @return Guild|null
     */
    private function setLatestGuild(User $user): ?Guild
    {
        $tenantDatabag = jaxon()->getResponse()->bag('tenant');

        // First try to get the current guild id from the databag.
        $guild = null;
        $guildId = $tenantDatabag->get('guild.id', 0);
        if($guildId > 0 &&
            ($guild = $this->guildService->getUserOrGuestGuild($guildId)) !== null)
        {
            $this->tenantService->setGuild($guild);
            return $guild;
        }

        // Try to get the latest guild the user worked on.
        if(($guildId = $user->properties['latest']['guild'] ?? 0) > 0)
        {
            $guild = $this->guildService->getUserOrGuestGuild($guildId);
        }
        if(!$guild)
        {
            $guild = $this->guildService->getFirstGuild();
        }
        if($guild !== null)
        {
            $tenantDatabag->set('guild.id', $guild->id);
            $tenantDatabag->set('round.id', 0);
            $this->tenantService->setGuild($guild);
        }
        return $guild;
    }

    /**
     * Get the latest guild round, from the session or the database.
     *
     * @param Guild $guild
     *
     * @return Round|null
     */
    private function setLatestRound(Guild $guild): ?Round
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
        if(($roundId = $guild->user->properties['latest']['round'] ?? 0) > 0)
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
            $tenantDatabag->set('guild.id', $guild->id);
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

        if(($guild = $this->setLatestGuild($user)) !== null)
        {
            $this->setLatestRound($guild);
        }
        view()->share('guild', $guild);

        return $next($request);
    }
}
