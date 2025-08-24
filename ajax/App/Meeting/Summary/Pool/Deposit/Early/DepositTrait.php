<?php

namespace Ajax\App\Meeting\Summary\Pool\Deposit\Early;

use Ajax\App\Meeting\Summary\Pool\Deposit\Total;
use Jaxon\App\Stash\Stash;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\Pool\EarlyDepositService;

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
     * @var EarlyDepositService
     */
    protected EarlyDepositService $depositService;

    /**
     * @return void
     */
    protected function showTotal(): void
    {
        $pool = $this->stash()->get('summary.pool');
        $session = $this->stash()->get('summary.session');
        $nextSession = $this->stash()->get('summary.early.session');
        [$amount, $count] = $this->depositService
            ->getPoolDepositNumbers($pool, $session, $nextSession);

        $this->stash()->set('summary.pool.deposit.count', $count);
        $this->stash()->set('summary.pool.deposit.amount', $amount);

        $this->cl(Total::class)->render();
    }

    /**
     * @return void
     */
    protected function getNextSession(): void
    {
        if($this->target()->method() === 'pool')
        {
            $this->bag('summary')->set('summary.early.session',
                $this->target()->args()[1]);
        }
        $session = $this->stash()->get('summary.session');
        $nextSessionId = (int)$this->bag('summary')->get('summary.early.session');
        $nextSession = $this->poolService->getNextSession($session, $nextSessionId);
        if(!$nextSession)
        {
            throw new MessageException(trans('tontine.session.errors.not_found'));
        }

        $this->stash()->set('summary.early.session', $nextSession);
    }
}
