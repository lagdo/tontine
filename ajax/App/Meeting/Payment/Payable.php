<?php

namespace Ajax\App\Meeting\Payment;

use Ajax\Component;
use App\Events\OnPagePaymentPayables;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Service\Payment\PaymentService;
use Siak\Tontine\Service\Meeting\Session\SessionService;
use Stringable;

#[Before('checkHostAccess', ["meeting", "payments"])]
#[Databag('meeting.payment')]
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
        $session = $this->stash()->get('payable.session');
        $member = $this->stash()->get('payable.member');
        // Get receivables, bills and debts.
        $payables = $this->paymentService->getPayables($session, $member);
        $this->stash()->set('payable.data', $payables);

        return $this->renderView('pages.meeting.payment.payables', $payables);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('payment-payables-home');
        $this->response->jo('tontine')->showSmScreen('payment-payables-home', 'payment-sm-screens');

        $payables = $this->stash()->get('payable.data');
        OnPagePaymentPayables::dispatch($payables);
    }

    public function show(int $memberId, int $sessionId): void
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
