<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\Base\Guild\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\RoundService;
use Siak\Tontine\Service\Guild\SessionService;

#[Before('checkHostAccess', ["guild", "calendar"])]
#[Before('getRound')]
#[Databag('guild.calendar')]
class SessionPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning', 'session.page'];

    public function __construct(private RoundService $roundService,
        private SessionService $sessionService)
    {}

    protected function getRound()
    {
        $roundId = $this->bag('guild.calendar')->get('round.id');
        $round = $this->roundService->getRound($this->guild(), $roundId);
        $this->stash()->set('guild.calendar.round', $round);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->stash()->get('guild.calendar.round');
        return $this->roundService->getSessionCount($round);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $round = $this->stash()->get('guild.calendar.round');

        return $this->renderTpl('pages.guild.calendar.session.page', [
            'sessions' => $round === null ? []:
                $this->roundService->getSessions($round, $this->currentPage()),
            'statuses' => $this->sessionService->getSessionStatuses(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-planning-sessions-page');
    }
}
