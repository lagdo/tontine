<?php

namespace Ajax\Page;

use Ajax\Component;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\TenantService;
use Stringable;

/**
 * @exclude
 */
class MainTitle extends Component
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
        $guild = $this->tenantService->guild();
        return $this->renderView('parts.header.title', [
            'guild' => $guild,
            'round' => $this->tenantService->round(),
            'guildCount' => $this->guildService->getGuildCount() +
                $this->guildService->getGuestGuildCount(),
            'roundCount' => $guild?->rounds()->count() ?? 0,
        ]);
    }
}
