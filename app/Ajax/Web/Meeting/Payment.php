<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\CallableClass;
use App\Events\OnPagePaymentHome;
use App\Events\OnPagePaymentPayables;
use Illuminate\Support\Collection;
use Siak\Tontine\Service\Meeting\PaymentService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\MemberService;

use function Jaxon\jq;
use function Jaxon\pm;
use function Jaxon\rq;
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
     * @param PaymentService $paymentService
     */
    public function __construct(private MemberService $memberService,
        private SessionService $sessionService, private PaymentService $paymentService)
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

        OnPagePaymentHome::dispatch();
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
        $this->response->call('makeTableResponsive', 'payment-members-page');

        // Don't show the payable items if there is no opened session or no member.
        if($this->sessions->count() === 0 || $this->memberService->getMemberCount('') === 0)
        {
            return $this->response;
        }
        $sessionId = pm()->select('select-session')->toInt();
        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $this->jq('.btn-member-payables')->click($this->rq()->payables($memberId, $sessionId));

        return $this->response;
    }

    public function payables(int $memberId, int $sessionId)
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

        [$receivables, $bills, $debts] = $this->paymentService->getPayables($member, $session);

        $html = $this->render('pages.meeting.payment.items',
            compact('member', 'session', 'receivables', 'debts', 'bills'));
        $this->response->html('payment-payables-home', $html);
        $this->response->call('makeTableResponsive', 'payment-payables-home');

        $this->response->call('showSmScreen', 'payment-payables-home', 'payment-sm-screens');
        $this->jq('#btn-payment-members-back')->click(rq('.')
            ->showSmScreen('payment-members-home', 'payment-sm-screens'));

        OnPagePaymentPayables::dispatch($member, $session, $receivables, $bills, $debts);
        return $this->response;
    }
}
