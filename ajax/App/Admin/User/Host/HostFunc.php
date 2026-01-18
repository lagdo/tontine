<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Base\FuncComponent;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Guild\UserService;
use Siak\Tontine\Validation\Guild\GuestInviteValidator;

use function Jaxon\form;

#[Databag('user')]
class HostFunc extends FuncComponent
{
    /**
     * @var GuestInviteValidator
     */
    protected GuestInviteValidator $validator;

    /**
     * @param UserService $userService
     */
    public function __construct(private UserService $userService)
    {}

    public function add(): void
    {
        $title = trans('tontine.invite.titles.add');
        $content = $this->renderTpl('pages.admin.user.host.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(form('invite-form')),
        ]];

        $this->modal()->hide();
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function create(array $formValues): void
    {
        $values = $this->validator->validateItem($formValues);
        $user = $this->tenantService->user();
        $this->userService->createInvite($user, $values['email']);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.sent'));

        $this->cl(HostPage::class)->page();
    }

    public function cancel(int $inviteId): void
    {
        $user = $this->tenantService->user();
        $this->userService->cancelInvite($user, $inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.cancelled'));

        $this->cl(HostPage::class)->page();
    }

    public function delete(int $inviteId): void
    {
        $user = $this->tenantService->user();
        $this->userService->deleteHostInvite($user, $inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

        $this->cl(HostPage::class)->page();
    }
}
