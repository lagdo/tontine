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
            $this->bag('subscription')->set('pool.id', $this->target()->args()[0]);
        }

        $poolId = (int)$this->bag('subscription')->get('pool.id');
        $this->cache()->set('subscription.pool', $this->poolService->getPool($poolId));
    }
}
