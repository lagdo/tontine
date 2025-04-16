<?php

namespace Ajax\App\Planning\Finance\Pool;

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
            $poolId = $this->target()->args()[0];
            $this->bag('planning.finance.pool')->set('pool.id', $poolId);
        }

        $round = $this->tenantService->round();
        $poolId = (int)$this->bag('planning.finance.pool')->get('pool.id');
        $pool = $this->poolService->getPool($round, $poolId);
        $this->stash()->set('planning.finance.pool', $pool);
    }
}
