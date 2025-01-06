<?php

namespace Ajax\App\Report\Session\Saving;

use Ajax\Component;
use Illuminate\Support\Collection;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\FundService;
use Stringable;

use function trim;

/**
 * @databag report
 * @before getSession
 * @before getFund
 */
class Fund extends Component
{
    /**
     * @var Collection
     */
    protected Collection $savings;

    /**
     * @var bool
     */
    protected bool $backButton = false;

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param SessionService $sessionService
     * @param ProfitService $profitService
     * @param ClosingService $closingService
     */
    public function __construct(private FundService $fundService,
        private SessionService $sessionService, private ProfitService $profitService,
        private ClosingService $closingService)
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
        $fundId = $this->bag('report')->get('fund.id', 0);
        $this->stash()->set('report.fund', $this->fundService->getFund($fundId, true, true));
    }

    protected function before()
    {
        $session = $this->stash()->get('report.session');
        $fund = $this->stash()->get('report.fund');
        $profit = $this->stash()->get('report.profit');
        if($profit === null)
        {
            $profit = $this->closingService->getProfitAmount($session, $fund);
            $this->stash()->set('report.profit', $profit);
        }
        $this->stash()->set('report.savings',
            $this->profitService->getDistributions($session, $fund, $profit));
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-report-fund-savings');
        $this->response->js('Tontine')->makeTableResponsive('content-fund-savings-distribution');
    }

    public function html(): Stringable
    {
        return $this->renderView('pages.report.session.savings.fund', [
            'fund' => $this->stash()->get('report.fund'),
            'profitAmount' => $this->stash()->get('report.profit'),
            'backButton' => $this->backButton,
        ]);
    }

    public function fund(int $fundId, string $item = '')
    {
        return $this->item(trim($item))->render();
    }

    public function amount(int $profitAmount)
    {
        $this->stash()->set('report.profit', $profitAmount);
        $this->render();

        return $this->response;
    }
}
