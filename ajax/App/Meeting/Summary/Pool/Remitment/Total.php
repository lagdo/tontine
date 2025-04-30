<?php

namespace Ajax\App\Meeting\Summary\Pool\Remitment;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;
use Stringable;

/**
 * @exclude
 */
class Total extends Component
{
    /**
     * @param BalanceCalculator $balanceCalculator
     * @param RemitmentService $remitmentService
     */
    public function __construct(private BalanceCalculator $balanceCalculator,
        private RemitmentService $remitmentService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');

        return $this->renderView('pages.meeting.summary.remitment.payable.total', [
            'pool' => $pool,
            'amount' => $this->balanceCalculator->getPayableAmount($pool, $session),
            'total' => $this->remitmentService->getRemitmentAmount($pool, $session),
            'count' => $this->remitmentService->getRemitmentCount($pool, $session),
        ]);
    }
}
