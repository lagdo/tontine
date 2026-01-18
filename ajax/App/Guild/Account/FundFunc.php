<?php

namespace Ajax\App\Guild\Account;

use Ajax\Base\Guild\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Guild\FundService;
use Siak\Tontine\Validation\Guild\FundValidator;

use function Jaxon\form;
use function trans;

#[Before('checkHostAccess', ["finance", "accounts"])]
#[Databag('guild.account')]
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
        $content = $this->renderTpl('pages.guild.account.fund.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(form('fund-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function create(array $formValues): void
    {
        $values = $this->validator->validateItem($formValues);

        $this->fundService->createFund($this->guild(), $values);
        $this->cl(FundPage::class)->page(); // Back to current page

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.created'));
    }

    public function edit(int $fundId): void
    {
        $fund = $this->fundService->getFund($this->guild(), $fundId);

        $title = trans('tontine.fund.titles.edit');
        $content = $this->renderTpl('pages.guild.account.fund.edit', ['fund' => $fund]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($fund->id, form('fund-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function update(int $fundId, array $formValues): void
    {
        $values = $this->validator->validateItem($formValues);

        $fund = $this->fundService->getFund($this->guild(), $fundId);
        $this->fundService->updateFund($fund, $values);
        $this->cl(FundPage::class)->page(); // Back to current page

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.updated'));
    }

    public function toggle(int $fundId): void
    {
        $fund = $this->fundService->getFund($this->guild(), $fundId);
        $this->fundService->toggleFund($fund);

        $this->cl(FundPage::class)->page();
    }

    public function delete(int $fundId): void
    {
        $fund = $this->fundService->getFund($this->guild(), $fundId);
        if($fund->funds_count > 0)
        {
            // A fund that is already in use cannot be deleted.
            return;
        }
        $this->fundService->deleteFund($this->guild(), $fund);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.fund.messages.deleted'));

        $this->cl(FundPage::class)->page();
    }
}
