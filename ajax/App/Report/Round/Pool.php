<?php

namespace Ajax\App\Report\Round;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Session\SummaryService;
use Stringable;

use function trans;

/**
 * @before checkHostAccess ["report", "round"]
 * @before checkOpenedSessions
 */
class Pool extends Component
{
    /**
     * @var SummaryService
     */
    protected $summaryService;

    public function html(): Stringable
    {
        $figures = $this->stash()->get('report.round.figures');
        return $this->renderView('pages.report.round.pool', $figures);
    }

    /**
     * @di $summaryService
     *
     * @param int $poolId
     * @param int $sessionId
     *
     * @return void
     */
    public function refresh(int $poolId, int $sessionId): void
    {
        $round = $this->stash()->get('tenant.round');
        $pool = $round->pools()
            ->withCount('subscriptions')
            ->find($poolId);
        if(!$pool || $pool->subscriptions_count === 0)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.pool.errors.not_found'));
            return;
        }

        $session = $round->sessions()->find($sessionId);
        $figures = $this->summaryService->getFigures($round, $session, $pool->id);
        $this->stash()->set('report.round.figures', $figures[0]);
        $this->item("pool-{$pool->id}")->render();
    }
}
