<?php

namespace Ajax\App\Planning\Subscription;

trait PoolTrait
{
    /**
     * @return void
     */
    protected function getPool()
    {
        if($this->target()->method() === 'pool')
        {
            $poolId = $this->target()->args()[0];
            $this->bag('subscription')->set('pool.id', $poolId);
        }

        $round = $this->tenantService->round();
        $poolId = (int)$this->bag('subscription')->get('pool.id');
        $pool = $this->poolService->getPool($round, $poolId);
        $this->stash()->set('subscription.pool', $pool);
    }
}
