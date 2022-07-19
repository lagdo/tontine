<?php

namespace App\Ajax\App\Planning;

use Siak\Tontine\Service\FundService;
use Siak\Tontine\Validation\Planning\FundValidator;
use App\Ajax\CallableClass;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use function intval;
use function jq;
use function pm;
use function trans;

class Fund extends CallableClass
{
    /**
     * @di
     * @var FundService
     */
    public FundService $fundService;

    /**
     * @var FundValidator
     */
    protected FundValidator $validator;

    /**
     * @var bool
     */
    protected bool $fromHome = false;

    /**
     * @databag fund
     * @databag subscription
     */
    public function home()
    {
        $html = $this->view()->render('pages.planning.fund.home');
        $this->response->html('section-title', trans('tontine.menus.planning'));
        $this->response->html('content-home', $html);
        $this->jq('#btn-refresh')->click($this->rq()->home());
        $this->jq('#btn-create')->click($this->rq()->number());

        $this->fromHome = true;
        return $this->page($this->bag('fund')->get('page', 1));
    }

    /**
     * @databag fund
     * @databag subscription
     */
    public function page(int $pageNumber = 0)
    {
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('fund')->get('page', 1);
        }
        $this->bag('fund')->set('page', $pageNumber);

        $funds = $this->fundService->getFunds($pageNumber);
        $fundCount = $this->fundService->getFundCount();

        $html = $this->view()->render('pages.planning.fund.page')->with('funds', $funds)
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $fundCount));
        $this->response->html('fund-page', $html);
        if($this->fromHome && $fundCount > 0)
        {
            // Show the subscriptions of the first fund in the list
            $fundId = $this->bag('subscription')->get('fund.id', $funds[0]->id);
            $this->response->script($this->cl(Subscription::class)->rq()->home($fundId));
        }

        $fundId = jq()->parent()->attr('data-fund-id')->toInt();
        $this->jq('.btn-fund-edit')->click($this->rq()->edit($fundId));
        $this->jq('.btn-fund-subscriptions')->click($this->cl(Subscription::class)->rq()->home($fundId));

        return $this->response;
    }

    public function number()
    {
        $title = trans('number.labels.title');
        $content = $this->view()->render('pages.planning.fund.number');
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
        $this->bag('faker')->set('fund.count', $count);

        $html = $this->view()->render('pages.planning.fund.add')->with('count', $count);
        $this->response->html('content-home', $html);
        $this->jq('#btn-cancel')->click($this->rq()->home());
        $this->jq('#btn-fakes')->click($this->rq()->fakes());
        $this->jq('#btn-save')->click($this->rq()->create(pm()->form('fund-form')));

        return $this->response;
    }

    /**
     * @databag faker
     */
    public function fakes()
    {
        $count = intval($this->bag('faker')->get('fund.count'));
        $funds = $this->fundService->getFakeFunds($count);
        for($i = 0; $i < $count; $i++)
        {
            $this->jq("#fund_title_$i")->val($funds[$i]->title);
            $this->jq("#fund_amount_$i")->val($funds[$i]->amount);
            $this->jq("#fund_notes_$i")->val($funds[$i]->notes);
        }

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $this->validator->validateList($formValues);

        $this->fundService->createFunds($formValues['funds'] ?? []);
        $this->notify->success(trans('tontine.fund.messages.created'), trans('common.titles.success'));

        return $this->home();
    }

    public function edit(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId);

        $title = trans('tontine.fund.labels.edit');
        $content = $this->view()->render('pages.planning.fund.edit')
            ->with('fund', $fund)
            ->with('locales', LaravelLocalization::getSupportedLocales());
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($fund->id, pm()->form('fund-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function update(int $fundId, array $formValues)
    {
        $this->validator->validateItem($formValues);

        $fund = $this->fundService->getFund($fundId);

        $this->fundService->updateFund($fund, $formValues);
        $this->page(); // Back to current page
        $this->dialog->hide();
        $this->notify->success(trans('tontine.fund.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }

    /*public function delete(int $fundId)
    {
        $this->notify->error("Cette fonction n'est pas encore disponible", trans('common.titles.error'));

        return $this->response;
    }*/
}
