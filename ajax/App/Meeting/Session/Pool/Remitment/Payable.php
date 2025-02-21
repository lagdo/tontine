<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\Component;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\BalanceCalculator;
use Stringable;

/**
 * @before getPool
 */
class Payable extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = Remitment::class;

    /**
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(protected BalanceCalculator $balanceCalculator)
    {}

    public function pool(int $poolId)
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');

        return $this->renderView('pages.meeting.remitment.payable.home', [
            'pool' => $pool,
            'depositAmount' => $this->balanceCalculator->getPoolDepositAmount($pool, $session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PayablePage::class)->render();
    }
}
