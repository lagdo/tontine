<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Late;

use Ajax\App\Meeting\Summary\Pool\Deposit\Total;
use Jaxon\App\Stash\Stash;
use Siak\Tontine\Service\Meeting\Pool\LateDepositService;

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
     * @var LateDepositService
     */
    protected LateDepositService $depositService;

    /**
     * @return void
     */
    protected function showTotal(): void
    {
        $session = $this->stash()->get('summary.session');
        $pool = $this->stash()->get('summary.pool');
        [$amount, $count] = $this->depositService->getPoolDepositNumbers($pool, $session);

        $this->stash()->set('summary.pool.deposit.count', $count);
        $this->stash()->set('summary.pool.deposit.amount', $amount);

        $this->cl(Total::class)->render();
    }
}
