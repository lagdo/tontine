<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SummaryService;

use function intval;

/**
 * @databag subscription
 * @before getPool
 */
class Planning extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @di
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @var SummaryService
     */
    public SummaryService $summaryService;

    /**
     * @return void
     */
    protected function getPool()
    {
        $poolId = $this->target()->method() === 'pool' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->cache->set('planning.pool', $this->poolService->getPool($poolId));
    }

    /**
     * @di $summaryService
     */
    public function home(): ComponentResponse
    {
        $pool = $this->cache->get('planning.pool');
        if(!$pool || !$pool->remit_planned)
        {
            return $this->response;
        }

        return $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $pool = $this->cache->get('planning.pool');
        $this->view()->shareValues($this->summaryService->getReceivables($pool));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.planning.subscription.planning', [
            'pool' => $this->cache->get('planning.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page');
    }
}
