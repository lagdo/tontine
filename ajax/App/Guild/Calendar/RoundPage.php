<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\RoundService;
use Stringable;

/**
 * @databag guild.calendar
 * @before checkHostAccess ["guild", "calendar"]
 */
class RoundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['guild.calendar', 'round.page'];

    /**
     * @param RoundService $roundService
     */
    public function __construct(protected RoundService $roundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $guild = $this->stash()->get('tenant.guild');
        return $this->roundService->getRoundCount($guild);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $guild = $this->stash()->get('tenant.guild');
        return $this->renderView('pages.guild.calendar.round.page', [
            'rounds' => $this->roundService->getRounds($guild, $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')
            ->makeTableResponsive('content-planning-rounds-page');
    }
}
