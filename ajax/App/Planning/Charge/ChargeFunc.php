<?php

namespace Ajax\App\Planning\Charge;

use Ajax\App\Planning\FuncComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\ChargeService;

use function trans;

/**
 * @databag planning.charge
 */
class ChargeFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    public function __construct(private ChargeService $chargeService)
    {}

    public function enable(int $defId)
    {
        $round = $this->stash()->get('tenant.round');
        $this->chargeService->enableCharge($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.charge.messages.enabled'));

        $this->cl(ChargePage::class)->page();
        $this->cl(ChargeCount::class)->render();
    }

    public function disable(int $defId)
    {
        $round = $this->stash()->get('tenant.round');
        $this->chargeService->disableCharge($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.charge.messages.removed'));

        $this->cl(ChargePage::class)->page();
        $this->cl(ChargeCount::class)->render();
    }
}
