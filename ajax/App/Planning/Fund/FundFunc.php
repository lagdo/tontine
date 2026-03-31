<?php

namespace Ajax\App\Planning\Fund;

use Ajax\App\Planning\FuncComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\FundService;

use function trans;

#[Databag('planning.fund')]
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
        $this->fundService->enableFund($this->round(), $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.enabled'));

        $this->cl(FundPage::class)->page();
        $this->cl(FundCount::class)->render();
    }

    public function disable(int $defId): void
    {
        $this->fundService->disableFund($this->round(), $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.disabled'));

        $this->cl(FundPage::class)->page();
        $this->cl(FundCount::class)->render();
    }
}
