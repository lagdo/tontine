<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\Component;
use Siak\Tontine\Service\Meeting\FundService;
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
        $session = $this->stash()->get('summary.session');
        $fundId = $this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $fundId > 0 ? $this->fundService->getSessionFund($session, $fundId) : null;
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    /**
     * @exclude
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderView('pages.meeting.summary.saving.home', [
            'session' => $session,
            'fundId' => (int)$this->bag('meeting.saving')->get('fund.id', 0),
            'funds' => $this->fundService->getSessionFundList($session)->prepend('', 0),
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
