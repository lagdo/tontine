<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Jaxon\App\Stash\Stash;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Meeting\Pool\DepositService;

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
     * @var DepositService
     */
    #[Inject]
    protected DepositService $depositService;

    /**
     * @return void
     */
    protected function showTotal(): void
    {
        $session = $this->stash()->get('meeting.session');
        $pool = $this->stash()->get('meeting.pool');
        [$amount, $count, $total] = $this->depositService->getPoolDepositNumbers($pool, $session);

        $this->stash()->set('meeting.pool.deposit.count', $count);
        $this->stash()->set('meeting.pool.deposit.amount', $amount);
        $this->stash()->set('meeting.pool.deposit.total', $total);

        $this->cl(Total::class)->render();
        $this->cl(Action::class)->render();
    }
}
