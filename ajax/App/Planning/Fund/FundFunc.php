<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\FuncComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\FundService;

use function trans;

/**
 * @databag planning.fund
 */
class FundFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    public function __construct(private FundService $fundService)
    {}

    public function enable(int $defId): void
    {
        $round = $this->stash()->get('tenant.round');
        $this->fundService->enableFund($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.enabled'));

        $this->cl(FundPage::class)->page();
        $this->cl(FundCount::class)->render();
    }

    public function disable(int $defId): void
    {
        $round = $this->stash()->get('tenant.round');
        $this->fundService->disableFund($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.disabled'));

        $this->cl(FundPage::class)->page();
        $this->cl(FundCount::class)->render();
    }
}
