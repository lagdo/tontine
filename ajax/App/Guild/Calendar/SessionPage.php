<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\PageComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\RoundService;
use Siak\Tontine\Service\Guild\SessionService;
use Stringable;

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
        $guild = $this->stash()->get('tenant.guild');
        $roundId = $this->bag('guild.calendar')->get('round.id');
        $round = $this->roundService->getRound($guild, $roundId);
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
    public function html(): Stringable
    {
        $round = $this->stash()->get('guild.calendar.round');

        return $this->renderView('pages.guild.calendar.session.page', [
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
