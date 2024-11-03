<?php

namespace App\Ajax\Web\Planning\Pool;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\Planning\PoolValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag pool
 */
class Pool extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

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

    /**
     * @before checkGuestAccess ["planning", "pools"]
     * @after hideMenuOnMobile
     */
    public function home(): ComponentResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.planning'));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.planning.pool.home', [
            'tontine' => $this->tenantService->tontine(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PoolPage::class)->page();
    }

    public function showIntro()
    {
        $title = trans('tontine.pool.titles.add');
        $content = $this->renderView('pages.planning.pool.add_intro');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.next'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->showDepositFixed(),
        ]];
        $this->dialog->show($title, $content, $buttons);
        $this->bag('pool')->set('add', []);

        return $this->response;
    }

    public function showDepositFixed()
    {
        $this->dialog->hide();

        $title = trans('tontine.pool.titles.deposits');
        $properties = $this->bag('pool')->get('add', []);
        $content = $this->renderView('pages.planning.pool.deposit_fixed', [
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function saveDepositFixed(bool $fixed)
    {
        $properties = $this->bag('pool')->get('add', []);
        $properties['deposit']['fixed'] = $fixed;
        $this->bag('pool')->set('add', $properties);

        if($fixed)
        {
            return $this->showDepositLendable();
        }
        // Pools with libre deposits are not lendable.
        return $this->saveDepositLendable(false);
    }

    public function showDepositLendable()
    {
        $this->dialog->hide();

        $properties = $this->bag('pool')->get('add', []);
        $title = trans('tontine.pool.titles.deposits');
        $content = $this->renderView('pages.planning.pool.deposit_lendable', [
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function saveDepositLendable(bool $lendable)
    {
        $properties = $this->bag('pool')->get('add', []);
        $properties['deposit']['lendable'] = $lendable;
        $this->bag('pool')->set('add', $properties);

        return $this->showRemitPlanned();
    }

    public function showRemitPlanned()
    {
        $this->dialog->hide();

        $title = trans('tontine.pool.titles.remitments');
        $properties = $this->bag('pool')->get('add', []);
        $fixed = $properties['deposit']['fixed'] ?? true;

        $content = $this->renderView('pages.planning.pool.remit_planned', [
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function saveRemitPlanned(bool $planned)
    {
        $properties = $this->bag('pool')->get('add', []);
        $properties['remit']['planned'] = $planned;
        $this->bag('pool')->set('add', $properties);

        return $this->showRemitAuction();
    }

    public function showRemitAuction()
    {
        $this->dialog->hide();

        $properties = $this->bag('pool')->get('add', []);

        $title = trans('tontine.pool.titles.remitments');
        $content = $this->renderView('pages.planning.pool.remit_auction', [
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function saveRemitAuction(bool $auction)
    {
        $properties = $this->bag('pool')->get('add', []);
        $properties['remit']['auction'] = $auction;
        $this->bag('pool')->set('add', $properties);

        return $this->add();
    }

    public function add()
    {
        $this->dialog->hide();

        $title = trans('tontine.pool.titles.add');
        $content = $this->renderView('pages.planning.pool.add', [
            'properties' => $this->bag('pool')->get('add', []),
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @databag subscription
     * @di $validator
     */
    public function create(array $formValues)
    {
        $formValues['properties'] = $this->bag('pool')->get('add', []);
        $values = $this->validator->validateItem($formValues);
        $this->poolService->createPool($values);

        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.created'));

        return $this->cl(PoolPage::class)->page();
    }

    public function edit(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);
        $title = trans('tontine.pool.titles.edit');
        $content = $this->renderView('pages.planning.pool.edit', [
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function update(int $poolId, array $formValues)
    {
        $pool = $this->poolService->getPool($poolId);
        // The properties field cannot be changed.
        $formValues['properties'] = $pool->properties;
        $values = $this->validator->validateItem($formValues);
        $this->poolService->updatePool($pool, $values);

        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.updated'));

        return $this->cl(PoolPage::class)->page();
    }

    public function delete(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);
        $this->poolService->deletePool($pool);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.pool.messages.deleted'));

        return $this->cl(PoolPage::class)->page();
    }
}
