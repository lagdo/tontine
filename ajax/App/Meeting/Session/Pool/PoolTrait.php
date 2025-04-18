<?php

namespace Ajax\App\Meeting\Session\Pool;

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
            $this->bag('meeting')->set('pool.id', $this->target()->args()[0]);
        }
        $session = $this->stash()->get('meeting.session');
        $poolId = (int)$this->bag('meeting')->get('pool.id');
        $pool = $this->poolService->getPool($poolId, $session);
        if(!$pool)
        {
            throw new MessageException(trans('tontine.session.errors.disabled'));
        }

        $this->stash()->set('meeting.pool', $pool);
    }
}
