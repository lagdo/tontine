<?php

namespace Ajax\App\Report\Session\Saving;

use Ajax\Component;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Stringable;

/**
 * @exclude
 */
class Summary extends Component
{
    /**
     * @param ProfitService $profitService
     */
    public function __construct(private ProfitService $profitService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('report.session');
        $fund = $this->stash()->get('report.fund');

        return $this->renderView('pages.report.session.savings.summary', [
            'profitAmount' => $this->stash()->get('report.profit'),
            'distribution' => $this->stash()->get('report.savings.distribution'),
            'amounts' => $this->profitService->getSavingAmounts($session, $fund),
        ]);
    }
}
