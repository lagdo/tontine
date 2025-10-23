<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Early;

use Ajax\App\Meeting\Session\Pool\Deposit\Total;
use Jaxon\App\Stash\Stash;
use Jaxon\Attributes\Attribute\Inject;
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
     * @var EarlyDepositService
     */
    #[Inject]
    protected EarlyDepositService $depositService;

    /**
     * @return void
     */
    protected function showTotal(): void
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $nextSession = $this->stash()->get('meeting.early.session');
        [$amount, $count] = $this->depositService
            ->getPoolDepositNumbers($pool, $session, $nextSession);

        $this->stash()->set('meeting.pool.deposit.count', $count);
        $this->stash()->set('meeting.pool.deposit.amount', $amount);

        $this->cl(Total::class)->render();
    }

    /**
     * @return void
     */
    protected function getNextSession(): void
    {
        if($this->target()->method() === 'pool')
        {
            $this->bag('meeting')->set('session.early.session',
                $this->target()->args()[1]);
        }
        $session = $this->stash()->get('meeting.session');
        $nextSessionId = (int)$this->bag('meeting')->get('session.early.session');
        $nextSession = $this->poolService->getNextSession($session, $nextSessionId);
        if(!$nextSession)
        {
            throw new MessageException(trans('tontine.session.errors.not_found'));
        }

        $this->stash()->set('meeting.early.session', $nextSession);
    }
}
