<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\App\Planning\Pool\PoolTrait;
use Ajax\App\Planning\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SummaryService;
use Stringable;

#[Before('getPool')]
#[Databag('planning.pool')]
#[Export(base: ['render'])]
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
    protected function before(): void
    {
        $pool = $this->stash()->get('planning.pool');
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
        $pool = $this->stash()->get('planning.pool');
        $this->view()->shareValues($this->summaryService->getReceivables($pool));

        return $this->renderView('pages.planning.pool.subscription.planning', [
            'pool' => $pool,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('Tontine')->makeTableResponsive('content-subscription-planning');
    }
}
