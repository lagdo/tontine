<?php

namespace Siak\Tontine\Service\Payment;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Report\MemberService;

class PaymentService
{
    /**
     * @param MemberService $memberService
     */
    public function __construct(private MemberService $memberService)
    {}

    /**
     * @param Session $session
     * @param Member $member
     *
     * @return array<Collection>
     */
    public function getPayables(Session $session, Member $member): array
    {
        return [
            'session' => $session,
            'member' => $member,
            'receivables' => $this->memberService->getReceivables($session, $member),
            'bills' => $this->memberService->getBills($session, $member),
            'debts' => $this->memberService->getDebts($session, $member),
        ];
    }
}
