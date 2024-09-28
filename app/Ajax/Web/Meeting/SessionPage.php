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
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page');
    }

    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.session.list.page', [
            'sessions' => $this->sessionService->getSessions($this->page),
            'statuses' => $this->sessionService->getSessionStatuses(),
        ]);
    }

    protected function count(): int
    {
        return $this->sessionService->getSessionCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        return $this->response;
    }
}
