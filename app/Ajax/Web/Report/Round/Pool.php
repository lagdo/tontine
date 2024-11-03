<?php

namespace App\Ajax\Web\Report\Round;

use App\Ajax\Component;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Meeting\SummaryService;

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

    public function html(): string
    {
        return $this->renderView('pages.report.round.pool', $this->figures);
    }

    /**
     * @di $summaryService
     *
     * @param int $poolId
     *
     * @return AjaxResponse
     */
    public function refresh(int $poolId): AjaxResponse
    {
        $round = $this->tenantService->round();
        $pool = $round->pools()
            ->withCount('subscriptions')
            ->find($poolId);
        if(!$pool || $pool->subscriptions_count === 0)
        {
            $this->notify
                ->title(trans('common.titles.error'))
                ->error(trans('tontine.pool.errors.not_found'));
            return $this->response;
        }

        $figures = $this->summaryService->getFigures($round, $pool->id);
        $this->figures = $figures[0];

        return $this->item("pool-{$pool->id}")->render();
    }
}
