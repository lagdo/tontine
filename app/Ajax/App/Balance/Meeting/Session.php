<?php

namespace App\Ajax\App\Balance\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Summary\MemberService as MemberSummaryService;
use Siak\Tontine\Service\Meeting\Summary\SessionService as SessionSummaryService;
use Siak\Tontine\Service\Meeting\SessionService;

use function compact;
use function Jaxon\pm;

class Session extends CallableClass
{
    /**
     * @di
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     * @di
     * @var MemberSummaryService
     */
    protected MemberSummaryService $memberSummaryService;

    /**
     * @di
     * @var SessionSummaryService
     */
    protected SessionSummaryService $sessionSummaryService;

    public function home()
    {
        // Don't show the page if there is no session or no member.
        $sessions = $this->sessionService->getSessions();
        if($sessions->count() === 0)
        {
            return $this->response;
        }
        $members = $this->sessionService->getMembers();
        if($members->count() === 0)
        {
            return $this->response;
        }
        $members->prepend('', 0);

        $tontine = $this->sessionService->getTontine();
        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $html = $this->view()->render('tontine.pages.meeting.summary.home',
            compact('sessions', 'members', 'tontine'));
        $this->response->html('content-home', $html);

        $this->jq('#btn-members-refresh')->click($this->rq()->home());
        $sessionId = pm()->select('select-session')->toInt();
        $memberId = pm()->select('select-member')->toInt();
        $this->jq('#btn-member-select')->click($this->rq()->show($sessionId, $memberId));

        return $this->show($sessions->keys()->first(), $members->keys()->first());
    }

    public function show(int $sessionId, int $memberId)
    {
        $tontine = $this->sessionService->getTontine();
        $session = $this->sessionService->getSession($sessionId);
        if($memberId === 0)
        {
            $this->deposits($session);
            $this->remitments($session);
            if($tontine->is_financial)
            {
                $this->loans($session);
                $this->refunds($session);
            }
            $this->fees($session);
            $this->fines($session);

            return $this->response;
        }

        return $this->cl(Member::class)->show($session, $this->sessionService->getMember($memberId),
            $tontine->is_financial, $this->memberSummaryService);
    }

    private function deposits(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.deposits', [
            'pools' => $this->sessionSummaryService->getDeposits($session),
        ]);
        $this->response->html('member-deposits', $html);
    }

    private function remitments(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.remitments', [
            'pools' => $this->sessionSummaryService->getRemitments($session),
        ]);
        $this->response->html('member-remitments', $html);
    }

    private function loans(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.loans', [
            'loan' => $this->sessionSummaryService->getLoan($session),
        ]);
        $this->response->html('member-loans', $html);
    }

    private function refunds(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.refunds', [
            'refund' => $this->sessionSummaryService->getRefund($session),
        ]);
        $this->response->html('member-refunds', $html);
    }

    private function fees(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.fees', [
            'fees' => $this->sessionSummaryService->getFees($session),
        ]);
        $this->response->html('member-fees', $html);
    }

    private function fines(SessionModel $session)
    {
        $html = $this->view()->render('tontine.pages.meeting.summary.session.fines', [
            'fines' => $this->sessionSummaryService->getFines($session),
        ]);
        $this->response->html('member-fines', $html);
    }
}
