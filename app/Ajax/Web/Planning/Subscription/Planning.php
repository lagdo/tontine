<?php

namespace App\Ajax\Web\Planning\Subscription;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SummaryService;

use function intval;

/**
 * @databag subscription
 * @before setPool
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
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

    /**
     * @return void
     */
    protected function setPool()
    {
        $poolId = $this->target()->method() === 'pool' ? $this->target()->args()[0] :
            intval($this->bag('subscription')->get('pool.id'));
        $this->pool = $this->poolService->getPool($poolId);
    }

    /**
     * @di $summaryService
     */
    public function home(): ComponentResponse
    {
        if(!$this->pool || !$this->pool->remit_planned)
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
        $this->view()->shareValues($this->summaryService->getReceivables($this->pool));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.planning.subscription.planning', [
            'pool' => $this->pool,
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
