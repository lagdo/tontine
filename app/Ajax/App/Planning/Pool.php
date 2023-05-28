<?php

namespace App\Ajax\App\Planning;

use Siak\Tontine\Service\Tontine\PoolService;
use Siak\Tontine\Validation\Planning\PoolValidator;
use App\Ajax\App\Faker;
use App\Ajax\CallableClass;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use function Jaxon\jq;
use function Jaxon\pm;
use function config;
use function trans;

class Pool extends CallableClass
{
    /**
     * @di
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @var PoolValidator
     */
    protected PoolValidator $validator;

    /**
     * @var bool
     */
    protected bool $fromHome = false;

    /**
     * @databag pool
     * @databag subscription
     */
    public function home()
    {
        $html = $this->view()->render('tontine.pages.planning.pool.home');
        $this->response->html('section-title', trans('tontine.menus.planning'));
        $this->response->html('content-home', $html);
        $this->jq('#btn-refresh')->click($this->rq()->home());
        $this->jq('#btn-create')->click($this->rq()->number());

        $this->fromHome = true;
        return $this->page($this->bag('pool')->get('page', 1));
    }

    /**
     * @databag pool
     */
    public function page(int $pageNumber = 0)
    {
        $poolCount = $this->poolService->getPoolCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $poolCount, 'pool', 'page');
        $pools = $this->poolService->getPools($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $poolCount);

        $html = $this->view()->render('tontine.pages.planning.pool.page')
            ->with('pools', $pools)
            ->with('pagination', $pagination);
        $this->response->html('pool-page', $html);

        if($this->fromHome && $poolCount > 0)
        {
            // Show the subscriptions of the first pool in the list
            $this->cl(Subscription::class)->show($pools[0]);
        }

        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-edit')->click($this->rq()->edit($poolId));
        $this->jq('.btn-pool-subscriptions')->click($this->cl(Subscription::class)->rq()->home($poolId));
        $this->jq('.btn-pool-delete')->click($this->rq()->delete($poolId)
            ->confirm(trans('tontine.pool.questions.delete')));

        return $this->response;
    }

    public function number()
    {
        $title = trans('number.labels.title');
        $content = $this->view()->render('tontine.pages.planning.pool.number');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.add'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->add(pm()->input('text-number')->toInt()),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @databag faker
     */
    public function add(int $count)
    {
        if($count <= 0)
        {
            $this->notify->warning(trans('number.errors.invalid'));
            return $this->response;
        }
        if($count > 10)
        {
            $this->notify->warning(trans('number.errors.max', ['max' => 10]));
            return $this->response;
        }

        $this->dialog->hide();

        $useFaker = config('jaxon.app.faker');
        $html = $this->view()->render('tontine.pages.planning.pool.add')
            ->with('useFaker', $useFaker)
            ->with('count', $count);
        $this->response->html('content-home', $html);
        $this->jq('#btn-cancel')->click($this->rq()->home());
        $this->jq('#btn-save')->click($this->rq()->create(pm()->form('pool-form')));
        if($useFaker)
        {
            $this->bag('faker')->set('pool.count', $count);
            $this->jq('#btn-fakes')->click($this->cl(Faker::class)->rq()->pools());
        }

        return $this->response;
    }

    /**
     * @databag pool
     * @databag subscription
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateList($formValues['pools'] ?? []);

        $this->poolService->createPools($values);
        $this->notify->success(trans('tontine.pool.messages.created'), trans('common.titles.success'));

        return $this->home();
    }

    public function edit(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);

        $title = trans('tontine.pool.titles.edit');
        $content = $this->view()->render('tontine.pages.planning.pool.edit')
            ->with('pool', $pool)
            ->with('locales', LaravelLocalization::getSupportedLocales());
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
        $values = $this->validator->validateItem($formValues);
        $pool = $this->poolService->getPool($poolId);

        $this->poolService->updatePool($pool, $values);

        $this->dialog->hide();
        // Back to current page
        $this->page();
        $this->notify->success(trans('tontine.pool.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }

    public function delete(int $poolId)
    {
        $pool = $this->poolService->getPool($poolId);
        if($pool->subscriptions()->count() > 0)
        {
            $this->notify->error(trans('tontine.pool.errors.delete.subscriptions'), trans('common.titles.error'));
            return $this->response;
        }

        $this->poolService->deletePool($pool);

        return $this->page();
    }
}
