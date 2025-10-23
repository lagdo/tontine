<?php

namespace Ajax\App\Planning\Pool;

use Ajax\App\Planning\FuncComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\LocaleService;

use function trans;

#[Databag('planning.pool')]
class PoolFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    public function __construct(private PoolService $poolService)
    {}

    public function enable(int $defId): void
    {
        $round = $this->stash()->get('tenant.round');
        $this->poolService->enablePool($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.enabled'));

        $this->cl(PoolPage::class)->page();
        $this->cl(PoolCount::class)->render();
    }

    public function disable(int $defId): void
    {
        $round = $this->stash()->get('tenant.round');
        $this->poolService->disablePool($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.disabled'));

        $this->cl(PoolPage::class)->page();
        $this->cl(PoolCount::class)->render();
    }
}
