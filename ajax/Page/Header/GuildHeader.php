<?php

namespace Ajax\Page\Header;

use Ajax\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\TenantService;
use Stringable;

#[Exclude]
class GuildHeader extends Component
{
    /**
     * @param TenantService $tenantService
     * @param GuildService $guildService
     */
    public function __construct(TenantService $tenantService,
        private GuildService $guildService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $user = $this->tenantService->user();
        $guild = $this->tenantService->guild();
        return $this->renderView('parts.header.guild', [
            'guild' => $guild,
            'round' => $this->tenantService->round(),
            'guildCount' => $this->guildService->getGuildCount($user) +
                $this->guildService->getGuestGuildCount($user),
            'roundCount' => $guild?->rounds()->count() ?? 0,
        ]);
    }
}
