<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Libre;

use App\Ajax\Component;
use Siak\Tontine\Service\BalanceCalculator;

/**
 * @exclude
 */
class MemberTotal extends Component
{
    /**
     * The constructor
     *
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(private BalanceCalculator $balanceCalculator)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.charge.libre.member.total', [
            'settlementCount' => $this->cache->get('meeting.session.settlement.count'),
            'settlementAmount' => $this->cache->get('meeting.session.settlement.amount'),
        ]);
    }
}
