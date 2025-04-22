<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\PageComponent;
use Siak\Tontine\Service\Guild\RoundService;
use Siak\Tontine\Service\Guild\SessionService;
use Stringable;

/**
 * @databag planning.calendar
 * @before getRound
 */
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
        $roundId = $this->bag('planning.calendar')->get('round.id');
        $this->stash()->set('planning.calendar.round', $this->roundService->getRound($roundId));
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->stash()->get('planning.calendar.round');
        return $this->roundService->getSessionCount($round);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->stash()->get('planning.calendar.round');

        return $this->renderView('pages.guild.calendar.session.page', [
            'sessions' => $round === null ? []:
                $this->roundService->getSessions($round, $this->currentPage()),
            'statuses' => $this->sessionService->getSessionStatuses(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-planning-sessions-page');
    }
}
