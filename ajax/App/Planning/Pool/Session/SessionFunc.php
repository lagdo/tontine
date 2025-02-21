<?php

namespace Ajax\App\Planning\Pool\Session;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Planning\PoolService;

/**
 * @databag pool.session
 * @before getPool
 */
class SessionFunc extends FuncComponent
{
    use PoolTrait;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(private PoolService $poolService)
    {}

    public function enableSession(int $sessionId)
    {
        $pool = $this->stash()->get('pool.session.pool');
        $this->poolService->enableSession($pool, $sessionId);

        $this->cl(SessionCounter::class)->render();
        $this->cl(SessionPage::class)->page();
    }

    public function disableSession(int $sessionId)
    {
        $pool = $this->stash()->get('pool.session.pool');
        $this->poolService->disableSession($pool, $sessionId);

        $this->cl(SessionCounter::class)->render();
        $this->cl(SessionPage::class)->page();
    }
}
