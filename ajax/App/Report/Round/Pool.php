<?php

namespace Ajax\App\Report\Round;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Meeting\Session\SummaryService;
use Stringable;

use function trans;

#[Before('checkHostAccess', ["report", "round"])]
#[Before('checkOpenedSessions')]
class Pool extends Component
{
    /**
     * @var SummaryService
     */
    protected SummaryService $summaryService;

    public function html(): Stringable
    {
        $figures = $this->stash()->get('report.round.figures');
        return $this->renderView('pages.report.round.pool', $figures);
    }

    /**
     * @param int $poolId
     * @param int $sessionId
     *
     * @return void
     */
    #[Inject(attr: 'summaryService')]
    public function refresh(int $poolId, int $sessionId): void
    {
        $pool = $this->round()->pools()
            ->withCount('subscriptions')
            ->find($poolId);
        if(!$pool || $pool->subscriptions_count === 0)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.pool.errors.not_found'));
            return;
        }

        $session = $this->round()->sessions()->find($sessionId);
        $figures = $this->summaryService->getFigures($this->round(), $session, $pool->id);
        $this->stash()->set('report.round.figures', $figures[0]);
        $this->item("pool-{$pool->id}")->render();
    }
}
