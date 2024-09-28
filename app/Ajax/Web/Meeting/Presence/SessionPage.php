<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\SessionService;

/**
 * @databag presence
 */
class SessionPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['presence', 'session.page'];

    /**
     * @param SessionService $sessionService
     * @param PresenceService $presenceService
     */
    public function __construct(private SessionService $sessionService,
        private PresenceService $presenceService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $member = $this->cl(Home::class)->getMember(); // Is null when showing presences by sessions.

        return (string)$this->renderView('pages.meeting.presence.session.page', [
            'member' => $member,
            'sessions' => $this->presenceService->getSessions($this->page),
            'absences' => !$member ? null :
                $this->presenceService->getMemberAbsences($member),
            'statuses' => $this->sessionService->getSessionStatuses(),
            'memberCount' => $this->presenceService->getMemberCount(),
        ]);
    }

    protected function count(): int
    {
        return $this->presenceService->getSessionCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('content-page-sessions');

        return $this->response;
    }
}
