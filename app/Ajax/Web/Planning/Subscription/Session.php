<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use Siak\Tontine\Service\Planning\PoolService;

/**
 * @databag subscription
 */
class Session extends Component
{
    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(private PoolService $poolService)
    {}

    public function html(): string
    {
        $pool = $this->cl(Home::class)->getPool();
        return (string)$this->renderView('pages.planning.subscription.session.home', [
            'pool' => $pool,
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
        $pool = $this->cl(Home::class)->getPool();
        $this->poolService->enableSession($pool, $sessionId);

        $this->cl(SessionCounter::class)->render();
        return $this->cl(SessionPage::class)->page();
    }

    public function disableSession(int $sessionId)
    {
        $pool = $this->cl(Home::class)->getPool();
        $this->poolService->disableSession($pool, $sessionId);

        $this->cl(SessionCounter::class)->render();
        return $this->cl(SessionPage::class)->page();
    }
}
