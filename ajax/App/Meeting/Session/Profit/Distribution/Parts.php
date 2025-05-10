<?php

namespace Ajax\App\Meeting\Session\Profit\Distribution;

use Ajax\Component;
use Siak\Tontine\Service\LocaleService;

use function trans;

/**
 * @exclude
 */
class Parts extends Component
{
    /**
     * @param LocaleService $localeService
     */
    public function __construct(private LocaleService $localeService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $distribution = $this->stash()->get('profit.savings.distribution');
        $profitAmount = $this->stash()->get('profit.amount');
        $html = trans('meeting.profit.distribution.amount', [
            'amount' => $this->localeService->formatMoney($profitAmount),
        ]);
        if($distribution->rewarded->count() > 0)
        {
            $html .= ' ' . trans('meeting.profit.distribution.parts', [
                'parts' => $distribution->savings->sum('parts'),
            ]);
        }
        return $html;
    }
}
