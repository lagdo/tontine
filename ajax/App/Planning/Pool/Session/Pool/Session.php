<?php

namespace Ajax\App\Planning\Pool\Session\Pool;

use Ajax\Component;
use Jaxon\Response\AjaxResponse;
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

    public function pool(int $poolId): AjaxResponse
    {
        return $this->render();
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.planning.pool.session.enabled.home', [
            'pool' => $this->cache()->get('pool.session.pool'),
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
        $pool = $this->cache()->get('pool.session.pool');
        $this->poolService->enableSession($pool, $sessionId);

        $this->cl(SessionCounter::class)->render();

        return $this->cl(SessionPage::class)->page();
    }

    public function disableSession(int $sessionId)
    {
        $pool = $this->cache()->get('pool.session.pool');
        $this->poolService->disableSession($pool, $sessionId);

        $this->cl(SessionCounter::class)->render();

        return $this->cl(SessionPage::class)->page();
    }
}
