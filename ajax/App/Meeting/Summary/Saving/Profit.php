<?php

namespace Ajax\App\Meeting\Summary\Saving;

use Ajax\App\Meeting\Summary\Component;
use Ajax\App\Report\Session\Saving\Profit as ProfitReport;
use Siak\Tontine\Service\Meeting\FundService;
use Stringable;

class Profit extends Component
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
        $fund = $this->fundService->getDefaultFund($session->round);

        // Save data for the report.
        $this->stash()->set('report.session', $session);
        $this->stash()->set('report.fund', $fund);
        $this->bag('report')->set('session.id', $session->id);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderView('pages.meeting.summary.saving.profit.home', [
            'session' => $session,
            'funds' => $this->fundService->getSessionFundList($session),
            'fund' => $this->fundService->getDefaultFund($session->round),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(ProfitReport::class)->render();

        $this->response->js('Tontine')->makeTableResponsive('content-session-profits');
    }

    /**
     * @exclude
     */
    public function show()
    {
        $this->render();
    }
}
