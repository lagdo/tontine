<?php

namespace App\Ajax\Web\Meeting\Session\Pool;

use Siak\Tontine\Exception\MessageException;

use function trans;

trait PoolTrait
{
    /**
     * @return void
     */
    protected function getPool()
    {
        if($this->target()->method() === 'pool')
        {
            $this->bag('meeting')->set('pool.id', $this->target()->args()[0]);
        }
        $poolId = (int)$this->bag('meeting')->get('pool.id');
        $pool = $this->poolService->getPool($poolId);

        $session = $this->cache->get('meeting.session');
        if(!$pool || $session->disabled($pool))
        {
            throw new MessageException(trans('tontine.session.errors.disabled'));
        }

        $this->cache->set('meeting.pool', $pool);
    }
}
