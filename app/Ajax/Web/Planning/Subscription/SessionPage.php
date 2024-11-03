<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Planning\PoolService;

/**
 * @databag subscription
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
    protected array $bagOptions = ['subscription', 'session.page'];

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
        $pool = $this->cache->get('subscription.pool');

        return $this->poolService->getPoolSessionCount($pool);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = $this->cache->get('subscription.pool');

        return (string)$this->renderView('pages.planning.subscription.session.page', [
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
