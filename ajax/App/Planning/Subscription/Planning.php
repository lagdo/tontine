<?php

namespace Ajax\App\Planning\Subscription;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SummaryService;
use Stringable;

/**
 * @databag subscription
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
    public function __construct(private PoolService $poolService,
        private SummaryService $summaryService)
    {}

    public function pool(int $poolId)
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $pool = $this->stash()->get('subscription.pool');
        $this->view()->shareValues($this->summaryService->getReceivables($pool));

        return $this->renderView('pages.planning.subscription.planning', [
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
