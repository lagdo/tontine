<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\SessionService;

use function Jaxon\jq;

/**
 * @databag presence
 */
class Session extends CallableClass
{
    /**
     * @var bool
     */
    private $fromHome = false;

    /**
     * 
     * @param PresenceService $presenceService
     */
    public function __construct(private SessionService $sessionService,
        private PresenceService $presenceService)
    {}

    public function home()
    {
        $html = $this->render('pages.meeting.presence.session.home');
        $this->response->html('content-home-sessions', $html);

        $this->jq('#btn-presence-sessions-refresh')->click($this->rq()->page());
        $this->fromHome = true;

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $sessionCount = $this->presenceService->getSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount,
            'presence', 'session.page');
        $sessions = $this->presenceService->getSessions($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $sessionCount);

        $html = $this->render('pages.meeting.presence.session.page', [
            'sessions' => $sessions,
            'pagination' => $pagination,
            'statuses' => $this->sessionService->getSessionStatuses(),
            'memberCount' => $this->presenceService->getMemberCount(),
        ]);
        $this->response->html('content-page-sessions', $html);

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-show-session-presence')
            ->click($this->cl(Member::class)->rq()->home($sessionId));

        if($this->fromHome && $sessions->count() > 0)
        {
            $session = $sessions->first();
            $this->cl(Member::class)->show($session);
        }

        return $this->response;
    }
}
