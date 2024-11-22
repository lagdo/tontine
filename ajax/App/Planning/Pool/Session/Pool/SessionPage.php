<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

use Ajax\PageComponent;
use Siak\Tontine\Service\Planning\PoolService;

/**
 * @databag pool.session
 * @before getPool
 */
class SessionPage extends PageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['pool.session', 'session.page'];

    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(protected PoolService $poolService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $pool = $this->cache->get('pool.session.pool');

        return $this->poolService->getPoolSessionCount($pool);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = $this->cache->get('pool.session.pool');

        return $this->renderView('pages.planning.pool.session.enabled.page', [
            'pool' => $pool,
            'sessions' => $this->poolService->getPoolSessions($pool, $this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('pool-subscription-sessions-page');
    }
}
