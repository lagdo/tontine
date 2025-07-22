<?php

namespace Ajax\App\Meeting\Session;

use Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

/**
 * @databag meeting
 * @before checkHostAccess ["meeting", "sessions"]
 */
class SessionPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'session.page'];

    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $round = $this->stash()->get('tenant.round');
        return $this->sessionService->getSessionCount($round);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $round = $this->stash()->get('tenant.round');
        return $this->renderView('pages.meeting.session.page', [
            'sessions' => $this->sessionService->getSessions($round, $this->currentPage()),
            'statuses' => $this->sessionService->getSessionStatuses(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-sessions-page');
    }
}
