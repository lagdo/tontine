<?php

namespace Ajax\App\Meeting\Session;

use Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\SessionService;
use Stringable;

/**
 * @databag session
 * @before checkHostAccess ["meeting", "sessions"]
 */
class SessionPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['session', 'page'];

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
        return $this->sessionService->getSessionCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.list.page', [
            'sessions' => $this->sessionService->getSessions($this->currentPage()),
            'statuses' => $this->sessionService->getSessionStatuses(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-page');
    }
}
