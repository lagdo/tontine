<?php

namespace Ajax\App\Meeting\Session\Profit;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;

#[Exclude]
class Fund extends Component
{
    /**
     * The constructor
     *
     * @param ProfitService $profitService
     */
    public function __construct(private ProfitService $profitService)
    {}

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $session = $this->stash()->get('profit.session');
        $fund = $this->stash()->get('profit.fund');
        $profit = $this->stash()->get('profit.amount');
        $this->stash()->set('profit.savings.distribution', $this->profitService
            ->getDistribution($session, $fund, $profit));

        // Show the fund title
        $this->response->html('content-report-profits-fund', $fund->title);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->response->jo('tontine')->makeTableResponsive('content-profit-distribution');
    }

    public function html(): string
    {
        return $this->renderTpl('pages.meeting.session.profit.fund', [
            'fund' => $this->stash()->get('profit.fund'),
            'profitAmount' => $this->stash()->get('profit.amount'),
        ]);
    }
}
