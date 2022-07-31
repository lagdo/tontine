<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\SessionService;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function trans;

/**
 * @databag session
 */
class Session extends CallableClass
{
    /**
     * @di
     * @var SessionService
     */
    public SessionService $sessionService;

    public function home()
    {
        $html = $this->view()->render('pages.meeting.home');
        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $this->response->html('content-home', $html);
        $this->jq('#btn-refresh')->click($this->rq()->home());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('session')->get('page', 1);
        }
        $this->bag('session')->set('page', $pageNumber);

        $sessions = $this->sessionService->getSessions($pageNumber);
        $sessionCount = $this->sessionService->getSessionCount();

        $html = $this->view()->render('pages.meeting.page')
            ->with('sessions', $sessions)
            ->with('members', $this->sessionService->getMembers())
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $sessionCount));
        $this->response->html('content-page', $html);

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-session-show')->click($this->cl(Meeting::class)->rq()->home($sessionId));

        return $this->response;
    }
}
