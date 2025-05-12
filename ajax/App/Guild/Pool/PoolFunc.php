<?php

namespace Ajax\App\Guild\Pool;

use Ajax\FuncComponent;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Service\Guild\PoolService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\Guild\PoolValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag guild.pool
 * @before checkHostAccess ["finance", "pools"]
 */
class PoolFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var PoolValidator
     */
    protected PoolValidator $validator;

    public function __construct(private PoolService $poolService)
    {}

    public function showIntro()
    {
        $title = trans('tontine.pool.titles.add');
        $content = $this->renderView('pages.guild.pool.add_intro', [
            'round' => $this->tenantService->round(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.next'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->showDepositFixed(),
        ]];
        $this->modal()->show($title, $content, $buttons);
        $this->bag('guild.pool')->set('add', []);
    }

    public function showDepositFixed()
    {
        $this->modal()->hide();

        $title = trans('tontine.pool.titles.deposits');
        $properties = $this->bag('guild.pool')->get('add', []);
        $content = $this->renderView('pages.guild.pool.deposit_fixed', [
            'fixed' => $properties['deposit']['fixed'] ?? true,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.next'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveDepositFixed(pm()->checked('pool_deposit_fixed')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function saveDepositFixed(bool $fixed)
    {
        $properties = $this->bag('guild.pool')->get('add', []);
        $properties['deposit']['fixed'] = $fixed;
        if($fixed)
        {
            $this->bag('guild.pool')->set('add', $properties);
            $this->showDepositLendable();
            return;
        }

        // Pools with libre deposits are not lendable.
        $properties['deposit']['lendable'] = false;
        $properties['remit']['planned'] = true;
        $properties['remit']['auction'] = false;
        $this->bag('guild.pool')->set('add', $properties);
        $this->add();
    }

    public function showDepositLendable()
    {
        $this->modal()->hide();

        $properties = $this->bag('guild.pool')->get('add', []);
        $title = trans('tontine.pool.titles.deposits');
        $content = $this->renderView('pages.guild.pool.deposit_lendable', [
            'lendable' => $properties['deposit']['lendable'] ?? false,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.prev'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->showDepositFixed(),
        ],[
            'title' => trans('common.actions.next'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveDepositLendable(pm()->checked('pool_deposit_lendable')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function saveDepositLendable(bool $lendable)
    {
        $properties = $this->bag('guild.pool')->get('add', []);
        $properties['deposit']['lendable'] = $lendable;
        $this->bag('guild.pool')->set('add', $properties);

        $this->showRemitPlanned();
    }

    public function showRemitPlanned()
    {
        $this->modal()->hide();

        $title = trans('tontine.pool.titles.remitments');
        $properties = $this->bag('guild.pool')->get('add', []);
        $fixed = $properties['deposit']['fixed'] ?? true;

        $content = $this->renderView('pages.guild.pool.remit_planned', [
            'planned' => $properties['remit']['planned'] ?? true,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.prev'),
            'class' => 'btn btn-primary',
            'click' => $fixed ? $this->rq()->showDepositLendable() : $this->rq()->showDepositFixed(),
        ],[
            'title' => trans('common.actions.next'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRemitPlanned(pm()->checked('pool_remit_planned')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function saveRemitPlanned(bool $planned)
    {
        $properties = $this->bag('guild.pool')->get('add', []);
        $properties['remit']['planned'] = $planned;
        $this->bag('guild.pool')->set('add', $properties);

        $this->showRemitAuction();
    }

    public function showRemitAuction()
    {
        $this->modal()->hide();

        $properties = $this->bag('guild.pool')->get('add', []);

        $title = trans('tontine.pool.titles.remitments');
        $content = $this->renderView('pages.guild.pool.remit_auction', [
            'auction' => $properties['remit']['auction'] ?? false,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.prev'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->showRemitPlanned(),
        ],[
            'title' => trans('common.actions.next'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRemitAuction(pm()->checked('pool_remit_auction')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function saveRemitAuction(bool $auction)
    {
        $properties = $this->bag('guild.pool')->get('add', []);
        $properties['remit']['auction'] = $auction;
        $this->bag('guild.pool')->set('add', $properties);

        $this->add();
    }

    public function add()
    {
        $this->modal()->hide();

        $title = trans('tontine.pool.titles.add');
        $content = $this->renderView('pages.guild.pool.add', [
            'properties' => $this->bag('guild.pool')->get('add', []),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('pool-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $formValues['properties'] = $this->bag('guild.pool')->get('add', []);
        $values = $this->validator->validateItem($formValues);
        $this->poolService->createPool($values);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.created'));

        $this->cl(PoolPage::class)->page();
    }

    public function edit(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);
        $title = trans('tontine.pool.titles.edit');
        $content = $this->renderView('pages.guild.pool.edit', [
            'pool' => $pool,
            'locales' => LaravelLocalization::getSupportedLocales(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($pool->id, pm()->form('pool-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $localeService
     * @di $validator
     */
    public function update(int $poolId, array $formValues)
    {
        $pool = $this->poolService->getPool($poolId);
        // The properties field cannot be changed.
        $formValues['properties'] = $pool->properties;
        if($pool->pools_count > 0)
        {
            // The amount of a pool that is already in use cannot be changed.
            $formValues['amount'] = $this->localeService->getMoneyValue($pool->amount);
        }
        $values = $this->validator->validateItem($formValues);
        $this->poolService->updatePool($pool, $values);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.updated'));

        $this->cl(PoolPage::class)->page();
    }

    public function delete(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);
        if($pool->pools_count > 0)
        {
            // A pool that is already in use cannot be deleted.
            return;
        }
        $this->poolService->deletePool($pool);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.deleted'));

        $this->cl(PoolPage::class)->page();
    }
}
