<?php

namespace Ajax\App\Admin\Guest;

use Ajax\PageComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\LocaleService;
use Stringable;

#[Databag('admin')]
class GuildPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['admin', 'guest.page'];

    /**
     * @param LocaleService $localeService
     * @param GuildService $guildService
     */
    public function __construct(private LocaleService $localeService,
        private GuildService $guildService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $user = $this->tenantService->user();
        return $this->guildService->getGuestGuildCount($user);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $user = $this->tenantService->user();
        $guilds = $this->guildService->getGuestGuilds($user, $this->currentPage());
        [$countries, $currencies] = $this->localeService->getNamesFromGuilds($guilds);
        return $this->renderView('pages.admin.user.guest.guild.page', [
            'guilds' => $guilds,
            'countries' => $countries,
            'currencies' => $currencies,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-page');
    }
}
