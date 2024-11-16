<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\Component;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Planning\PoolService;

/**
 * @databag subscription
 * @before getPool
 */
class Session extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = PoolSection::class;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(private PoolService $poolService)
    {}

    public function pool(int $poolId): AjaxResponse
    {
        $this->bag('subscription')->set('session.filter', false);

        return $this->render();
    }

    public function html(): string
    {
        return $this->renderView('pages.planning.subscription.session.home', [
            'pool' => $this->cache->get('subscription.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SessionPage::class)->page();
    }

    public function enableSession(int $sessionId)
    {
        $pool = $this->cache->get('subscription.pool');
        $this->poolService->enableSession($pool, $sessionId);

        $this->cl(SessionCounter::class)->render();

        return $this->cl(SessionPage::class)->page();
    }

    public function disableSession(int $sessionId)
    {
        $pool = $this->cache->get('subscription.pool');
        $this->poolService->disableSession($pool, $sessionId);

        $this->cl(SessionCounter::class)->render();

        return $this->cl(SessionPage::class)->page();
    }
}