<?php

namespace Ajax\App\Meeting\Session\Profit\Distribution;

use Ajax\Component;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;

use function trans;

/**
 * @exclude
 */
class Total extends Component
{
    /**
     * @param LocaleService $localeService
     * @param ProfitService $profitService
     */
    public function __construct(private LocaleService $localeService,
        private ProfitService $profitService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('profit.session');
        $fund = $this->stash()->get('profit.fund');
        $amounts = $this->profitService->getSavingAmounts($session, $fund);

        return trans('meeting.profit.distribution.total', [
            'saving' => $this->localeService->formatMoney($amounts['saving']),
            'refund' => $this->localeService->formatMoney($amounts['refund']),
        ]);
    }
}
