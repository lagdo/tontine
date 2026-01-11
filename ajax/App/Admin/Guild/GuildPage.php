<?php

namespace Ajax\App\Admin\Guild;

use Ajax\Base\PageComponent;
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
    protected array $bagOptions = ['admin', 'guild.page'];

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
        return $this->guildService->getGuildCount($user);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $user = $this->tenantService->user();
        $guilds = $this->guildService->getGuilds($user, $this->currentPage());
        [$countries, $currencies] = $this->localeService->getNamesFromGuilds($guilds);
        return $this->renderView('pages.admin.guild.page', [
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
        $this->response->jo('tontine')->makeTableResponsive('content-organisation-page');
    }
}
