<?php

namespace Ajax\App\Report\Session\Saving;

use Ajax\Component;
use Illuminate\Support\Collection;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Meeting\FundService;
use Stringable;

/**
 * @databag report
 * @before getSession
 * @before getFund
 */
class Profit extends Component
{
    /**
     * @var Collection
     */
    protected Collection $savings;

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param SessionService $sessionService
     * @param ProfitService $profitService
     */
    public function __construct(private FundService $fundService,
        private SessionService $sessionService, private ProfitService $profitService)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('report')->get('session.id');
        $this->stash()->set('report.session', $this->sessionService->getSession($sessionId));
    }

    /**
     * @return void
     */
    protected function getFund()
    {
        if($this->target()->method() === 'fund')
        {
            $this->bag('report')->set('fund.id', $this->target()->args()[0]);
        }
        $session = $this->stash()->get('report.session');
        $fundId = $this->bag('report')->get('fund.id', 0);
        $fund = $this->fundService->getSessionFund($session, $fundId);
        $this->stash()->set('report.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $session = $this->stash()->get('report.session');
        $fund = $this->stash()->get('report.fund');
        $profit = $this->stash()->get('report.profit');
        if($profit === null)
        {
            $profit = $fund->profit;
            $this->stash()->set('report.profit', $profit);
        }
        $this->stash()->set('report.savings.distribution',
            $this->profitService->getDistribution($session, $fund, $profit));
        // Show the fund title
        $this->response->html('content-report-profits-fund', $fund->title);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-report-profits');
        $this->response->js('Tontine')->makeTableResponsive('content-report-profit-distribution');
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.report.session.savings.fund', [
            'fund' => $this->stash()->get('report.fund'),
            'profitAmount' => $this->stash()->get('report.profit'),
        ]);
    }

    public function fund(int $fundId)
    {
        $this->render();
    }

    public function amount(int $profitAmount)
    {
        $this->stash()->set('report.profit', $profitAmount);
        $this->render();
    }
}
