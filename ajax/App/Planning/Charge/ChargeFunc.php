<?php

namespace Ajax\App\Planning\Charge;

use Ajax\App\Planning\FuncComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\ChargeService;

use function trans;

#[Databag('planning.charge')]
class ChargeFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    public function __construct(private ChargeService $chargeService)
    {}

    public function enable(int $defId): void
    {
        $this->chargeService->enableCharge($this->round(), $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.charge.messages.enabled'));

        $this->cl(ChargePage::class)->page();
        $this->cl(ChargeCount::class)->render();
    }

    public function disable(int $defId): void
    {
        $this->chargeService->disableCharge($this->round(), $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.charge.messages.removed'));

        $this->cl(ChargePage::class)->page();
        $this->cl(ChargeCount::class)->render();
    }
}
