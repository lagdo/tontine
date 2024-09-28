<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\PoolService;

use function intval;

/**
 * @databag subscription
 * @before getPool
 */
class Session extends Component
{
    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     */
    public function __construct(private PoolService $poolService)
    {}

    /**
     * @exclude
     *
     * @return PoolModel|null
     */
    public function getPool(): ?PoolModel
    {
        if(!$this->pool)
        {
            $poolId = intval($this->bag('subscription')->get('pool.id'));
            $this->pool = $this->poolService->getPool($poolId);
        }

        return $this->pool;
    }

    /**
     * @exclude
     */
    public function show(PoolModel $pool)
    {
        $this->pool = $pool;
        $this->refresh();
    }

    public function refresh()
    {
        $this->bag('subscription')->set('session.filter', false);

        $this->render();
        $this->cl(SessionPage::class)->page();

        return $this->response;
    }

    public function html(): string
    {
        return (string)$this->renderView('pages.planning.subscription.session.home', [
            'pool' => $this->pool,
        ]);
    }

    public function enableSession(int $sessionId)
    {
        $this->poolService->enableSession($this->pool, $sessionId);

        $this->cl(SessionCounter::class)->render();
        return $this->cl(SessionPage::class)->page();
    }

    public function disableSession(int $sessionId)
    {
        $this->poolService->disableSession($this->pool, $sessionId);

        $this->cl(SessionCounter::class)->render();
        return $this->cl(SessionPage::class)->page();
    }
}
