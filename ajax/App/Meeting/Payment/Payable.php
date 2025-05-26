<?php

namespace Ajax\App\Meeting\Payment;

use Ajax\Component;
use App\Events\OnPagePaymentPayables;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Service\Payment\PaymentService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

use function compact;

/**
 * @databag meeting.payment
 * @before checkHostAccess ["meeting", "payments"]
 */
class Payable extends Component
{
    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     * @param PaymentService $paymentService
     */
    public function __construct(private MemberService $memberService,
        private SessionService $sessionService, private PaymentService $paymentService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $member = $this->stash()->get('payable.member');
        $session = $this->stash()->get('payable.session');
        [$receivables, $bills, $debts] = $this->paymentService
            ->getPayables($member, $session);
        $this->stash()->set('payable.data', [$member, $session, $receivables, $bills, $debts]);

        return $this->renderView('pages.meeting.payment.payables',
            compact('member', 'session', 'receivables', 'debts', 'bills'));
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('payment-payables-home');
        $this->response->js('Tontine')->showSmScreen('payment-payables-home', 'payment-sm-screens');

        [$member, $session, $receivables, $bills, $debts] = $this->stash()->get('payable.data');
        OnPagePaymentPayables::dispatch($member, $session, $receivables, $bills, $debts);
    }

    public function show(int $memberId, int $sessionId)
    {
        $round = $this->stash()->get('tenant.round');
        if(!($member = $this->memberService->getMember($round, $memberId)))
        {
            return;
        }
        if(!($session = $this->sessionService->getSession($round, $sessionId)))
        {
            return;
        }

        $this->stash()->set('payable.member', $member);
        $this->stash()->set('payable.session', $session);
        $this->render();
    }
}
