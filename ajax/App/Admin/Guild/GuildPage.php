<?php

namespace Ajax\App\Admin\Guild;

use Ajax\PageComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\GuildService;
use Stringable;

/**
 * @databag tontine
 * @databag pool
 */
class GuildPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['tontine', 'page'];

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
        return $this->guildService->getGuildCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $guilds = $this->guildService->getGuilds($this->currentPage());
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
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-organisation-page');
    }
}
