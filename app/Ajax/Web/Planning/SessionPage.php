<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\PageComponent;
use Siak\Tontine\Model\Round as RoundModel;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Planning\SessionService;

/**
 * @databag planning
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

    /**
     * @return RoundModel|null
     */
    private function getRound(): ?RoundModel
    {
        $roundId = $this->bag('planning')->get('round.id');
        return $this->roundService->getRound($roundId);
    }

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->getRound();
        return $this->roundService->getSessionCount($round);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $round = $this->getRound();
        return (string)$this->renderView('pages.planning.session.page', [
            'sessions' => $round === null ? []:
                $this->roundService->getSessions($round, $this->page),
            'statuses' => $this->sessionService->getSessionStatuses(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page-sessions');
    }
}
