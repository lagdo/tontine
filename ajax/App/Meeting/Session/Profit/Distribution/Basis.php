<?php

namespace Ajax\App\Meeting\Session\Profit\Distribution;

use Ajax\Component;
use Siak\Tontine\Service\LocaleService;

use function trans;

/**
 * @exclude
 */
class Basis extends Component
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
        return $distribution->rewarded->count() < 2 ? '':
            trans('meeting.profit.distribution.basis', [
                'unit' => $this->localeService->formatMoney($distribution->partAmount),
            ]);
    }
}
