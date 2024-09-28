<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\Component;
use App\Events\OnPagePaymentPayables;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
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
     * @var MemberModel
     */
    private MemberModel $member;

    /**
     * @var SessionModel
     */
    private SessionModel $session;

    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     * @param PaymentService $paymentService
     */
    public function __construct(private MemberService $memberService,
        private SessionService $sessionService, private PaymentService $paymentService)
    {}

    public function html(): string
    {
        $member = $this->member;
        $session = $this->session;
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
        if(!($this->member = $this->memberService->getMember($memberId)))
        {
            return $this->response;
        }
        if(!($this->session = $this->sessionService->getSession($sessionId)))
        {
            return $this->response;
        }

        $this->render();

        return $this->response;
    }
}
