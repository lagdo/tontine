<?php

namespace Ajax\App\Planning\Fund;

use Ajax\FuncComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\FundService;

use function trans;

/**
 * @databag planning.finance.fund
 */
class FundFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    public function __construct(private FundService $fundService)
    {}

    public function enable(int $defId)
    {
        $round = $this->tenantService->round();
        $this->fundService->enableFund($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.enabled'));

        $this->cl(FundPage::class)->page();
    }

    public function disable(int $defId)
    {
        $round = $this->tenantService->round();
        $this->fundService->disableFund($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.disabled'));

        $this->cl(FundPage::class)->page();
    }
}
