<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\Base\Guild\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\RoundService;

#[Before('checkHostAccess', ["guild", "calendar"])]
#[Databag('guild.calendar')]
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
        return $this->roundService->getRoundCount($this->guild());
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.guild.calendar.round.page', [
            'rounds' => $this->roundService->getRounds($this->guild(), $this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')
            ->makeTableResponsive('content-planning-rounds-page');
    }
}
