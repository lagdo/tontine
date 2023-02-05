<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Planning\SessionService;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function trans;

/**
 * @databag session
 */
class Meeting extends CallableClass
{
    /**
     * @di
     * @var SessionService
     */
    public SessionService $sessionService;

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.home');
        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $this->response->html('content-home', $html);
        $this->jq('#btn-refresh')->click($this->rq()->home());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $sessionCount = $this->sessionService->getSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount, 'session', 'page');
        $sessions = $this->sessionService->getSessions($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $sessionCount);

        $statuses = [
            SessionModel::STATUS_PENDING => trans('tontine.session.status.pending'),
            SessionModel::STATUS_OPENED => trans('tontine.session.status.opened'),
            SessionModel::STATUS_CLOSED => trans('tontine.session.status.closed'),
        ];

        $html = $this->view()->render('tontine.pages.meeting.page')
            ->with('sessions', $sessions)
            ->with('statuses', $statuses)
            ->with('members', $this->sessionService->getMembers())
            ->with('pagination', $pagination);
        $this->response->html('content-page', $html);

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-session-show')->click($this->cl(Session::class)->rq()->home($sessionId));

        return $this->response;
    }
}
