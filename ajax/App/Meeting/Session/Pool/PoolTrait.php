<?php

namespace Ajax\App\Meeting\Session\Pool;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Request\TargetInterface;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function trans;

trait PoolTrait
{
    /**
     * @di
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * Get the Jaxon request target
     *
     * @return TargetInterface|null
     */
    abstract protected function target(): ?TargetInterface;

    /**
     * Get the temp cache
     *
     * @return Stash
     */
    abstract protected function stash(): Stash;

    /**
     * Get a data bag.
     *
     * @param string  $sBagName
     *
     * @return DataBagContext
     */
    abstract protected function bag(string $sBagName): DataBagContext;

    /**
     * @param bool $ofSession
     *
     * @return void
     */
    protected function getPool(bool $ofSession = true): void
    {
        if($this->target()->method() === 'pool')
        {
            $this->bag('meeting')->set('pool.id', $this->target()->args()[0]);
        }
        $session = $this->stash()->get('meeting.session');
        $poolId = (int)$this->bag('meeting')->get('pool.id');
        $pool = $ofSession ?
            $this->poolService->getPool($session, $poolId) :
            $this->poolService->getRoundPool($session, $poolId);
        if(!$pool)
        {
            throw new MessageException(trans('tontine.session.errors.disabled'));
        }

        $this->stash()->set('meeting.pool', $pool);
    }
}
