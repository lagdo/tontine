<?php

namespace Ajax\App\Planning\Pool\Session;

use Ajax\Component;
use Siak\Tontine\Service\Planning\PoolService;
use Stringable;

/**
 * @databag pool.session
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

    public function pool(int $poolId)
    {
        $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.session.active.home', [
            'pool' => $this->stash()->get('pool.session.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SessionPage::class)->page();

        $this->response->js('Tontine')
            ->showSmScreen('content-planning-sessions', 'pool-sm-screens');
    }

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
