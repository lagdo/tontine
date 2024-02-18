<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\CallableClass;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Report\MemberService as ReportService;
use Siak\Tontine\Service\Tontine\MemberService;

use function Jaxon\jq;
use function Jaxon\pm;
use function compact;
use function trans;

/**
 * @before checkGuestAccess ["meeting", "payments"]
 */
class Payment extends CallableClass
{
    /**
     * @var Collection
     */
    private Collection $sessions;

    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     * @param ReportService $reportService
     */
    public function __construct(private MemberService $memberService,
        private SessionService $sessionService, private ReportService $reportService)
    {}

    protected function getOpenedSessions()
    {
        $this->sessions = $this->sessionService->getRoundSessions(orderAsc: false)
            ->filter(fn($session) => $session->opened)
            ->pluck('title', 'id');
    }

    /**
     * @before getOpenedSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $html = $this->render('pages.meeting.payment.home', ['sessions' => $this->sessions]);
        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $this->response->html('content-home', $html);

        return $this->page($this->bag('payment')->get('page', 1));
    }

    /**
     * @before getOpenedSessions
     */
    public function page(int $pageNumber = 0)
    {
        $memberCount = $this->memberService->getMemberCount('');
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount, 'payment', 'page');
        $members = $this->memberService->getMembers('', $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);

        $html = $this->render('pages.meeting.payment.page', compact('members', 'pagination'));
        $this->response->html('payment-members-page', $html);

        // Don't show the page if there is no opened session or no member.
        if($this->sessions->count() === 0 || $this->memberService->getMemberCount('') === 0)
        {
            return $this->response;
        }
        $sessionId = pm()->select('select-session')->toInt();
        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $this->jq('.btn-member-payments')->click($this->rq()->payments($memberId, $sessionId));

        return $this->response;
    }

    /**
     * @param MemberModel $member
     * @param SessionModel $session
     *
     * @return array
     */
    private function getItems(MemberModel $member, SessionModel $session): array
    {
        $receivables = $this->reportService->getReceivables($session, $member);
        $debts = $this->reportService->getDebts($session, $member);
        $bills = $this->reportService->getBills($session, $member);

        // Count items not paid
        $unpaid = $receivables->reduce(fn($total, $receivable)
            => $receivable->paid ? $total : $total + 1, 0);
        $unpaid = $debts->reduce(fn($total, $debt)
            => $debt->refund ? $total : $total + 1, $unpaid);
        $unpaid = $bills->reduce(fn($total, $bill)
            => $bill->settlement ? $total : $total + 1, $unpaid);

        return [$unpaid, $receivables, $debts, $bills];
    }

    public function payments(int $memberId, int $sessionId)
    {
        if(!($member = $this->memberService->getMember($memberId)))
        {
            return $this->response;
        }
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            return $this->response;
        }
        if(!$session->opened)
        {
            return $this->response;
        }

        [$unpaid, $receivables, $debts, $bills] = $this->getItems($member, $session);
        $html = $this->render('pages.meeting.payment.items', compact('member', 'session',
            'receivables', 'debts', 'bills', 'unpaid'));
        $this->response->html('payment-payables-home', $html);
        $this->response->call('showPaymentDetails');
        $this->jq('#btn-payment-members-back')->click(pm()->js('showPaymentMembers'));

        return $this->response;
    }
}
