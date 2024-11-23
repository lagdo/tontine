<?php

namespace Ajax\App\Tontine\Invite;

use Ajax\Component;
use Siak\Tontine\Service\Tontine\InviteService;
use Siak\Tontine\Validation\Tontine\GuestInviteValidator;
use Stringable;

use function Jaxon\pm;

class Host extends Component
{
    /**
     * @var GuestInviteValidator
     */
    protected GuestInviteValidator $validator;

    /**
     * @param InviteService $inviteService
     */
    public function __construct(private InviteService $inviteService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.invite.host.home');
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
        $content = $this->renderView('pages.invite.host.add');
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
        $this->inviteService->createInvite($values['email']);

        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.sent'));

        return $this->cl(HostPage::class)->page();
    }

    public function cancel(int $inviteId)
    {
        $this->inviteService->cancelInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.cancelled'));

        return $this->cl(HostPage::class)->page();
    }

    public function delete(int $inviteId)
    {
        $this->inviteService->deleteHostInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

        return $this->cl(HostPage::class)->page();
    }
}
