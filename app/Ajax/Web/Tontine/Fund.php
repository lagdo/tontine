<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Tontine\FundValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag tontine
 */
class Fund extends CallableClass
{
    /**
     * @var FundValidator
     */
    protected FundValidator $validator;

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
        $html = $this->renderView('pages.options.fund.home');
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

        $html = $this->renderView('pages.options.fund.page')
            ->with('funds', $funds)
            ->with('pagination', $pagination);
        $this->response->html('fund-page', $html);
        $this->response->call('makeTableResponsive', 'fund-page');

        $fundId = jq()->parent()->attr('data-fund-id')->toInt();
        $this->jq('.btn-fund-edit')->click($this->rq()->edit($fundId));
        $this->jq('.btn-fund-toggle')->click($this->rq()->toggle($fundId));

        return $this->response;
    }

    public function add()
    {
        $title = trans('tontine.fund.titles.add');
        $content = $this->renderView('pages.options.fund.add');
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

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $this->fundService->createFund($values);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.fund.messages.created'), trans('common.titles.success'));

        return $this->response;
    }

    public function edit(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId);

        $title = trans('tontine.fund.titles.edit');
        $content = $this->renderView('pages.options.fund.edit')->with('fund', $fund);
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

    /**
     * @di $validator
     */
    public function update(int $fundId, array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $fund = $this->fundService->getFund($fundId);
        $this->fundService->updateFund($fund, $values);
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
