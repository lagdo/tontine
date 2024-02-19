<?php

namespace Siak\Tontine\Service\Meeting;

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
     * @param Member $member
     * @param Session $session
     *
     * @return array<Collection>
     */
    public function getPayables(Member $member, Session $session): array
    {
        $receivables = $this->memberService->getReceivables($session, $member);
        $debts = $this->memberService->getDebts($session, $member);
        $bills = $this->memberService->getBills($session, $member);

        return [$receivables, $bills, $debts];
    }
}
