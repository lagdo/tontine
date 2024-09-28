<?php

namespace App\Ajax\Web\Report;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\MemberService;

/**
 * @databag report
 */
class Session extends CallableClass
{
    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     */
    public function __construct(protected MemberService $memberService,
        protected SessionService $sessionService)
    {}

    /**
     * @before checkGuestAccess ["report", "session"]
     * @before checkOpenedSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.report'));

        $sessions = $this->sessionService->getRoundSessions(orderAsc: false)
            ->filter(fn($session) => ($session->opened || $session->closed));

        $html = $this->renderView('pages.report.session.home', [
            'sessions' => $sessions->pluck('title', 'id'),
            'members' => $this->memberService->getMemberList()->prepend('', 0),
        ]);
        $this->response->html('content-home', $html);

        return $sessions->count() === 0 ? $this->response:
            $this->_showSession($sessions->first());
    }

    private function _showSession(SessionModel $session, MemberModel $member = null)
    {
        $this->bag('report')->set('session.id', $session->id);

        $title = $session->title;
        if($member !== null)
        {
            $title .= ' - ' . $member->name;
        }
        $this->response->html('session-report-title', $title);

        // Initialize the page components.
        $this->cl(Session\Bill\Session::class)->init($session, $member);
        $this->cl(Session\Bill\Total::class)->init($session, $member);
        $this->cl(Session\Deposit::class)->init($session, $member);
        $this->cl(Session\Disbursement::class)->init($session, $member);
        $this->cl(Session\Loan::class)->init($session, $member);
        $this->cl(Session\Refund::class)->init($session, $member);
        $this->cl(Session\Remitment::class)->init($session, $member);
        $this->cl(Session\Saving::class)->init($session, $member);
        $this->cl(Session\Saving\Fund::class)->clear();

        $this->response->html('content-page', $this->renderView('pages.report.session.session'));

        // Render the page buttons.
        $this->cl(Session\Action\Export::class)->setSessionId($session->id)->render();
        $this->cl(Session\Action\Menu::class)->setSessionId($session->id)->render();

        return $this->response;
    }

    public function showSession(int $sessionId)
    {
        if($sessionId <= 0 ||
            !($session = $this->sessionService->getSession($sessionId)))
        {
            return $this->response;
        }

        return $this->_showSession($session);
    }

    public function showMember(int $sessionId, int $memberId)
    {
        if($sessionId <= 0 || $memberId <= 0 ||
            !($session = $this->sessionService->getSession($sessionId)) ||
            !($member = $this->memberService->getMember($memberId)))
        {
            return $this->response;
        }

        return $this->_showSession($session, $member);
    }
}
