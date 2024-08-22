<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\Web\Meeting\Session as Menu;
use Siak\Tontine\Service\Meeting\SessionService;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @before checkGuestAccess ["meeting", "sessions"]
 */
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
        $html = $this->renderView('pages.meeting.session.list.home');
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

        $html = $this->renderView('pages.meeting.session.list.page', [
            'sessions' => $sessions,
            'statuses' => $this->sessionService->getSessionStatuses(),
            'pagination' => $pagination
        ]);
        $this->response->html('content-page', $html);
        $this->response->call('makeTableResponsive', 'content-page');

        $rqSession = $this->rq(Session\Home::class);
        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-session-resync')->click($this->rq()->resync($sessionId)
            ->confirm(trans('tontine.session.questions.resync')));
        $this->jq('.btn-session-open')->click($this->rq()->open($sessionId)
            ->confirm(trans('tontine.session.questions.open') . '<br/>' .
            trans('tontine.session.questions.warning')));
        $this->jq('.btn-session-close')->click($this->rq()->close($sessionId)
            ->confirm(trans('tontine.session.questions.close')));

        $this->jq('.btn-session-summary')->click($this->rq()->summary($sessionId));

        $this->jq('.btn-session-pools')->click($rqSession->pools($sessionId));
        $this->jq('.btn-session-savings')->click($rqSession->savings($sessionId));
        $this->jq('.btn-session-credits')->click($rqSession->credits($sessionId));
        $this->jq('.btn-session-cash')->click($rqSession->cash($sessionId));
        $this->jq('.btn-session-charges')->click($rqSession->charges($sessionId));
        $this->jq('.btn-session-reports')->click($rqSession->reports($sessionId));

        return $this->response;
    }

    public function open(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)) || $session->opened)
        {
            $this->notify->error(trans('tontine.session.errors.opened'), trans('common.titles.error'));
            return $this->page();
        }

        $this->sessionService->openSession($session);

        return $this->page();
    }

    public function close(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)) || !$session->opened)
        {
            $this->notify->error(trans('tontine.session.errors.not_opened'), trans('common.titles.error'));
            return $this->page();
        }

        $this->sessionService->closeSession($session);

        return $this->page();
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

    public function summary(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            $this->notify->error(trans('tontine.session.errors.not_opened'), trans('common.titles.error'));
            return $this->page();
        }

        $this->bag('meeting')->set('session.id', $session->id);

        $html = $this->renderView('pages.meeting.summary.home', [
            'session' => $session,
        ]);
        $this->response->html('content-home', $html);
        $this->jq('a', '#session-tabs')->click(jq()->tab('show'));

        $this->jq('#btn-session-back')->click($this->rq(Menu::class)->home());
        $this->jq('#btn-session-refresh')->click($this->rq()->summary($session->id));

        $this->pools($session);
        $this->savings($session);
        $this->credits($session);
        $this->cash($session);
        $this->charges($session);

        return $this->response;
    }

    private function pools(SessionModel $session)
    {
        $this->cl(Summary\Pool\Deposit::class)->show($session);
        $this->cl(Summary\Pool\Remitment::class)->show($session);

        $this->response->call('setSmScreenHandler', 'session-pools-sm-screens');

        return $this->response;
    }

    private function savings(SessionModel $session)
    {
        $this->cl(Summary\Saving\Saving::class)->show($session);
        $this->cl(Summary\Saving\Closing::class)->show($session);

        $this->response->call('setSmScreenHandler', 'session-savings-sm-screens');

        return $this->response;
    }

    private function credits(SessionModel $session)
    {
        $this->cl(Summary\Credit\Loan::class)->show($session);
        $this->cl(Summary\Credit\PartialRefund::class)->show($session);
        $this->cl(Summary\Credit\Refund::class)->show($session);

        $this->response->call('setSmScreenHandler', 'session-credits-sm-screens');

        return $this->response;
    }

    private function cash(SessionModel $session)
    {
        $this->cl(Summary\Cash\Disbursement::class)->show($session);

        return $this->response;
    }

    private function charges(SessionModel $session)
    {
        $this->cl(Summary\Charge\FixedFee::class)->show($session);
        $this->cl(Summary\Charge\LibreFee::class)->show($session);

        $this->response->call('setSmScreenHandler', 'session-charges-sm-screens');

        return $this->response;
    }
}
