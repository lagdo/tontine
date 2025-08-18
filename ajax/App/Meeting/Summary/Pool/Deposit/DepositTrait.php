<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit;

use Jaxon\App\Stash\Stash;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Payment\BalanceCalculator;

trait DepositTrait
{
    /**
     * Get the temp cache
     *
     * @return Stash
     */
    abstract protected function stash(): Stash;

    /**
     * Get an instance of a Jaxon class by name
     *
     * @template T
     * @param class-string<T> $sClassName the class name
     *
     * @return T|null
     */
    abstract protected function cl(string $sClassName): mixed;

    /**
     * @di
     * @var DepositService
     */
    protected DepositService $depositService;

    /**
     * @di
     * @var BalanceCalculator
     */
    protected BalanceCalculator $balanceCalculator;

    private function showTotal(): void
    {
        $session = $this->stash()->get('summary.session');
        $pool = $this->stash()->get('summary.pool');
        $this->stash()->set('summary.pool.deposit.count',
            $this->depositService->getPoolDepositCount($pool, $session));
        $this->stash()->set('summary.pool.deposit.amount',
            $this->balanceCalculator->getPoolDepositAmount($pool, $session));

        $this->cl(Total::class)->render();
    }
}
