<?php

namespace Ajax\App\Guild\Options;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Guild\FundService;
use Siak\Tontine\Validation\Guild\FundValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag tontine
 */
class FundFunc extends FuncComponent
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

    public function add()
    {
        $title = trans('tontine.fund.titles.add');
        $content = $this->renderView('pages.guild.options.fund.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('fund-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $this->fundService->createFund($values);
        $this->cl(FundPage::class)->page(); // Back to current page

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.created'));
    }

    public function edit(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId);

        $title = trans('tontine.fund.titles.edit');
        $content = $this->renderView('pages.guild.options.fund.edit')->with('fund', $fund);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($fund->id, pm()->form('fund-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function update(int $fundId, array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $fund = $this->fundService->getFund($fundId);
        $this->fundService->updateFund($fund, $values);
        $this->cl(FundPage::class)->page(); // Back to current page

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.updated'));
    }

    public function toggle(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId);
        $this->fundService->toggleFund($fund);

        $this->cl(FundPage::class)->page();
    }

    public function delete(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId);
        if($fund->funds_count > 0)
        {
            // A fund that is already in use cannot be deleted.
            return;
        }
        $this->fundService->deleteFund($fund);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.deleted'));

        $this->cl(FundPage::class)->page();
    }
}
