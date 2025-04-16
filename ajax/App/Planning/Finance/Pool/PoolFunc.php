<?php

namespace Ajax\App\Planning\Finance\Pool;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\LocaleService;

use function trans;

/**
 * @databag pool
 */
class PoolFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    public function __construct(private PoolService $poolService)
    {}

    public function enable(int $defId)
    {
        $round = $this->tenantService->round();
        $this->poolService->enablePool($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.enabled'));

        $this->cl(PoolPage::class)->page();
    }

    public function disable(int $defId)
    {
        $round = $this->tenantService->round();
        $this->poolService->disablePool($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.disabled'));

        $this->cl(PoolPage::class)->page();
    }
}
