<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\SummaryService;
use Stringable;

use function trans;

class Pool extends Component
{
    /**
     * @var SummaryService
     */
    protected $summaryService;

    /**
     * The figures for a given pool
     *
     * @var array
     */
    private $figures;

    /**
     * @exclude
     */
    public function setFigures(array $figures): self
    {
        $this->figures = $figures;
        return $this;
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.report.round.pool', $this->figures);
    }

    /**
     * @di $summaryService
     *
     * @param int $poolId
     *
     * @return void
     */
    public function refresh(int $poolId)
    {
        $round = $this->tenantService->round();
        $pool = $round->pools()
            ->withCount('subscriptions')
            ->find($poolId);
        if(!$pool || $pool->subscriptions_count === 0)
        {
            $this->alert()
                ->title(trans('common.titles.error'))
                ->error(trans('tontine.pool.errors.not_found'));
            return;
        }

        $figures = $this->summaryService->getFigures($round, $pool->id);
        $this->figures = $figures[0];

        $this->item("pool-{$pool->id}")->render();
    }
}
