<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\SessionService;

use function Jaxon\jq;
use function trans;

class Session extends CallableClass
{
    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    /**
     * @before checkRoundSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $html = $this->render('pages.meeting.session.list');
        $this->response->html('content-home', $html);

        $this->jq('#btn-sessions-refresh')->click($this->rq()->page());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $sessionCount = $this->sessionService->getSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount, 'session', 'page');
        $sessions = $this->sessionService->getSessions($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $sessionCount);

        $html = $this->render('pages.meeting.session.page', [
            'sessions' => $sessions,
            'statuses' => $this->sessionService->getSessionStatuses(),
            'pagination' => $pagination
        ]);
        $this->response->html('content-page', $html);

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-session-show')->click($this->cl(Session\Home::class)->rq()->home($sessionId));
        $this->jq('.btn-session-resync')->click($this->rq()->resync($sessionId)
            ->confirm(trans('tontine.session.questions.resync')));

        return $this->response;
    }

    public function resync(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)) || !$session->opened)
        {
            $this->notify->error(trans('tontine.session.errors.not_opened'), trans('common.titles.error'));
            return $this->page();
        }

        $this->sessionService->resyncSession($session);

        $this->notify->success(trans('tontine.session.messages.resynced'), trans('common.titles.success'));
        return $this->page();
    }
}
