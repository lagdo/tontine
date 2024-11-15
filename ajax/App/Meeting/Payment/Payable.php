<?php

namespace Ajax\App\Meeting\Payment;

use Ajax\Component;
use App\Events\OnPagePaymentPayables;
use Siak\Tontine\Service\Meeting\PaymentService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\MemberService;

use function compact;

/**
 * @databag payment
 * @before checkGuestAccess ["meeting", "payments"]
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
    public function html(): string
    {
        $member = $this->cache->get('payable.member');
        $session = $this->cache->get('payable.session');
        [$receivables, $bills, $debts] = $this->paymentService->getPayables($member, $session);

        OnPagePaymentPayables::dispatch($member, $session, $receivables, $bills, $debts);

        return $this->renderView('pages.meeting.payment.items',
            compact('member', 'session', 'receivables', 'debts', 'bills'));
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('payment-payables-home');
        $this->response->js()->showSmScreen('payment-payables-home', 'payment-sm-screens');
    }

    public function show(int $memberId, int $sessionId)
    {
        if(!($member = $this->memberService->getMember($memberId)))
        {
            return $this->response;
        }
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            return $this->response;
        }

        $this->cache->set('payable.member', $member);
        $this->cache->set('payable.session', $session);
        $this->render();

        return $this->response;
    }
}
