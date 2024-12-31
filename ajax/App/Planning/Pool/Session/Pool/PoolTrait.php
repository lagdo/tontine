<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

trait PoolTrait
{
    /**
     * @return void
     */
    protected function getPool()
    {
        if($this->target()->method() === 'pool')
        {
            $this->bag('pool.session')->set('pool.id', $this->target()->args()[0]);
        }

        $poolId = (int)$this->bag('pool.session')->get('pool.id');
        $this->stash()->set('pool.session.pool', $this->poolService->getPool($poolId));
    }
}
