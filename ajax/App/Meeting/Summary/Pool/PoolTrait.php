<?php

namespace Ajax\App\Meeting\Summary\Pool;

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
     * @return void
     */
    protected function getPool()
    {
        if($this->target()->method() === 'pool')
        {
            $this->bag('summary')->set('pool.id', $this->target()->args()[0]);
        }
        $session = $this->stash()->get('summary.session');
        $poolId = (int)$this->bag('summary')->get('pool.id');
        $pool = $this->poolService->getPool($poolId, $session);
        if(!$pool)
        {
            throw new MessageException(trans('tontine.session.errors.disabled'));
        }

        $this->stash()->set('summary.pool', $pool);
    }
}
