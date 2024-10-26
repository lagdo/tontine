<?php

namespace App\Ajax\Web\Meeting\Session\Pool;

use App\Ajax\Cache;
use Siak\Tontine\Exception\MessageException;

use function trans;

trait PoolTrait
{
    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('pool.id');
        $pool = $this->poolService->getPool($poolId);

        $session = Cache::get('meeting.session');
        if(!$pool || $session->disabled($pool))
        {
            throw new MessageException(trans('tontine.session.errors.disabled'));
        }

        Cache::set('meeting.pool', $pool);
    }
}
