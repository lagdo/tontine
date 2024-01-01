<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Tontine\FundService;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag tontine
 */
class Fund extends CallableClass
{
    /**
     * @param FundService $fundService
     */
    public function __construct(protected FundService $fundService)
    {}

    /**
     * @exclude
     */
    public function show()
    {
        return $this->home();
    }

    public function home()
    {
        $html = $this->render('pages.options.fund.home');
        $this->response->html('content-funds-home', $html);

        $this->jq('#btn-fund-refresh')->click($this->rq()->home());
        $this->jq('#btn-fund-create')->click($this->rq()->add());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $fundCount = $this->fundService->getFundCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $fundCount, 'tontine', 'fund.page');
        $funds = $this->fundService->getFunds($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $fundCount);

        $html = $this->render('pages.options.fund.page')
            ->with('funds', $funds)
            ->with('pagination', $pagination);
        $this->response->html('fund-page', $html);

        $fundId = jq()->parent()->attr('data-fund-id')->toInt();
        $this->jq('.btn-fund-edit')->click($this->rq()->edit($fundId));
        $this->jq('.btn-fund-toggle')->click($this->rq()->toggle($fundId));

        return $this->response;
    }

    public function add()
    {
        $title = trans('tontine.fund.titles.add');
        $content = $this->render('pages.options.fund.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('fund-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function create(array $formValues)
    {
        $this->fundService->createFund($formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.fund.messages.created'), trans('common.titles.success'));

        return $this->response;
    }

    public function edit(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId);

        $title = trans('tontine.fund.titles.edit');
        $content = $this->render('pages.options.fund.edit')->with('fund', $fund);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($fund->id, pm()->form('fund-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function update(int $fundId, array $formValues)
    {
        $fund = $this->fundService->getFund($fundId);
        $this->fundService->updateFund($fund, $formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.fund.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }

    public function toggle(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId);
        $this->fundService->toggleFund($fund);

        return $this->page();
    }
}
