<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\SessionService;

/**
 * @databag session
 * @before checkGuestAccess ["meeting", "sessions"]
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
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.session.list.page', [
            'sessions' => $this->sessionService->getSessions($this->page),
            'statuses' => $this->sessionService->getSessionStatuses(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page');
    }
}
