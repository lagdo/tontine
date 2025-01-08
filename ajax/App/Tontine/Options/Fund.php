<?php

namespace Ajax\App\Tontine\Options;

use Ajax\Component;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Tontine\FundValidator;
use Stringable;

use function Jaxon\pm;
use function trans;

/**
 * @databag tontine
 */
class Fund extends Component
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

    public function html(): Stringable
    {
        return $this->renderView('pages.options.fund.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(FundPage::class)->page();
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
}
