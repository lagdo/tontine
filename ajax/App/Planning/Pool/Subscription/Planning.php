<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\App\Planning\Pool\PoolTrait;
use Ajax\App\Planning\Component;
use Ajax\Page\SectionContent;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SummaryService;
use Stringable;

/**
 * @databag planning.finance.pool
 * @before getPool
 */
class Planning extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param SummaryService $summaryService
     */
    public function __construct(PoolService $poolService,
        private SummaryService $summaryService)
    {
        $this->poolService = $poolService;
    }

    public function pool(int $poolId)
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $pool = $this->stash()->get('planning.finance.pool');
        if(!$pool->remit_planned)
        {
            throw new MessageException(trans('tontine.pool.errors.not_planned'));
        }
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('planning.finance.pool');
        $this->view()->shareValues($this->summaryService->getReceivables($pool));

        return $this->renderView('pages.planning.pool.subscription.planning', [
            'pool' => $pool,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-subscription-planning');
    }
}
