<?php

namespace Ajax\App\Meeting\Session\Profit\Distribution;

use Ajax\Base\Round\Component;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;

use function trans;

#[Exclude]
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
        $distribution = $this->stash()->get('profit.savings.distribution');
        $saving = $distribution->transfers->sum('amount');
        $session = $this->stash()->get('profit.session');
        $fund = $this->stash()->get('profit.fund');
        $refund = $this->profitService->getRefundsAmount($session, $fund);

        return trans('meeting.profit.distribution.total', [
            'saving' => $this->localeService->formatMoney($saving),
            'refund' => $this->localeService->formatMoney($refund),
        ]);
    }
}
