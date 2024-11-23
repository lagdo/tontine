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
        $session = $this->cache->get('report.session');
        $fund = $this->cache->get('report.fund');
        $savings = $this->cache->get('report.savings');

        return $this->renderView('pages.report.session.savings.summary', [
            'profitAmount' => $this->cache->get('report.profit'),
            'partUnitValue' => $this->profitService->getPartUnitValue($savings),
            'distributionSum' => $savings->sum('distribution'),
            'distributionCount' => $savings->filter(fn($saving) => $saving->distribution > 0)->count(),
            'amounts' => $this->profitService->getSavingAmounts($session, $fund),
        ]);
    }
}
