<?php

namespace App\Ajax\Web\Report\Session\Saving;

use App\Ajax\Component;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;

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
    public function html(): string
    {
        [$savings, $session, $fund, $profitAmount] = $this->cl(Fund::class)->getData();

        return (string)$this->renderView('pages.report.session.savings.summary', [
            'profitAmount' => $profitAmount,
            'partUnitValue' => $this->profitService->getPartUnitValue($savings),
            'distributionSum' => $savings->sum('distribution'),
            'distributionCount' => $savings->filter(fn($saving) => $saving->distribution > 0)->count(),
            'amounts' => $this->profitService->getSavingAmounts($session, $fund),
        ]);
    }
}
