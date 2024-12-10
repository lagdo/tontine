<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Component;
use Siak\Tontine\Service\Tontine\UserService;
use Siak\Tontine\Validation\Tontine\GuestInviteValidator;
use Stringable;

use function Jaxon\pm;

/**
 * @databag user
 */
class Host extends Component
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

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.user.host.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(HostPage::class)->page();
    }

    public function add()
    {
        $title = trans('tontine.invite.titles.add');
        $content = $this->renderView('pages.user.host.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('invite-form')),
        ]];

        $this->dialog->hide();
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->userService->createInvite($values['email']);

        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.sent'));

        return $this->cl(HostPage::class)->page();
    }

    public function cancel(int $inviteId)
    {
        $this->userService->cancelInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.cancelled'));

        return $this->cl(HostPage::class)->page();
    }

    public function delete(int $inviteId)
    {
        $this->userService->deleteHostInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

        return $this->cl(HostPage::class)->page();
    }
}
