<?php

namespace Ajax\App\Planning\Financial;

use Siak\Tontine\Service\Planning\PoolService;

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
            $this->bag('planning.financial')->set('pool.id', $this->target()->args()[0]);
        }

        $poolId = (int)$this->bag('planning.financial')->get('pool.id');
        $this->stash()->set('planning.financial.pool', $this->poolService->getPool($poolId));
    }
}
