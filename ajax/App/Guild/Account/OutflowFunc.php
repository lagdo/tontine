<?php

namespace Ajax\App\Guild\Account;

use Ajax\FuncComponent;
use Siak\Tontine\Model\Category as CategoryModel;
use Siak\Tontine\Service\Guild\AccountService;

use function Jaxon\pm;
use function trans;

/**
 * @databag guild.account
 * @before checkHostAccess ["finance", "accounts"]
 */
class OutflowFunc extends FuncComponent
{
    /**
     * @param AccountService $accountService
     */
    public function __construct(protected AccountService $accountService)
    {}

    public function add()
    {
        $types = [
            CategoryModel::TYPE_OUTFLOW => trans('tontine.account.types.outflow'),
        ];
        $title = trans('tontine.account.titles.add');
        $content = $this->renderView('pages.guild.account.outflow.add', [
            'types' => $types,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('account-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function create(array $formValues)
    {
        $this->accountService->createAccount($formValues);
        $this->cl(OutflowPage::class)->page(); // Back to current page

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.account.messages.created'));
    }

    public function edit(int $accountId)
    {
        $account = $this->accountService->getAccount($accountId);

        $title = trans('tontine.account.titles.edit');
        $types = [
            CategoryModel::TYPE_OUTFLOW => trans('tontine.account.types.outflow'),
        ];
        $content = $this->renderView('pages.guild.account.outflow.edit', [
            'types' => $types,
            'account' => $account,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($account->id, pm()->form('account-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function update(int $accountId, array $formValues)
    {
        $account = $this->accountService->getAccount($accountId);
        $this->accountService->updateAccount($account, $formValues);
        $this->cl(OutflowPage::class)->page(); // Back to current page

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.account.messages.updated'));
    }

    public function toggle(int $accountId)
    {
        $account = $this->accountService->getAccount($accountId);
        $this->accountService->toggleAccount($account);

        $this->cl(OutflowPage::class)->page();
    }

    public function delete(int $accountId)
    {
        $account = $this->accountService->getAccount($accountId);
        $this->accountService->deleteAccount($account);

        $this->cl(OutflowPage::class)->page();
    }
}
