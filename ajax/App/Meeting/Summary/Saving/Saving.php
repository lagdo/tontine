<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Guild\FundService;
use Stringable;

/**
 * @databag meeting.saving
 */
class Saving extends Component
{
    /**
     * The constructor
     *
     * @param FundService $fundService
     */
    public function __construct(protected FundService $fundService)
    {}

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $fundId = $this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $fundId > 0 ? $this->fundService->getFund($fundId, true, true) : null;
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    /**
     * @exclude
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.summary.saving.home', [
            'session' => $this->stash()->get('summary.session'),
            'fundId' => (int)$this->bag('meeting.saving')->get('fund.id', 0),
            'funds' => $this->fundService->getFundList()->prepend('', 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SavingPage::class)->page();
        $this->cl(SavingCount::class)->render();

        $this->response->js('Tontine')->showSmScreen('report-fund-savings', 'session-savings');
    }

    public function fund(int $fundId)
    {
        $this->bag('meeting.saving')->set('fund.id', $fundId);

        $this->render();
    }
}
