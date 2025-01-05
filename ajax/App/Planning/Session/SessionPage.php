<?php

namespace Ajax\App\Planning\Session;

use Ajax\PageComponent;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Planning\SessionService;
use Stringable;

/**
 * @databag planning
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
        $roundId = $this->bag('planning')->get('round.id');
        $this->stash()->set('planning.round', $this->roundService->getRound($roundId));
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->stash()->get('planning.round');
        return $this->roundService->getSessionCount($round);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->stash()->get('planning.round');

        return $this->renderView('pages.planning.round.session.page', [
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
        $this->response->js('Tontine')->makeTableResponsive('content-page-sessions');
    }
}
