<?php

namespace App\Ajax\Web\Tontine\Invite;

use App\Ajax\Component;
use Siak\Tontine\Service\Tontine\GuestService;
use Siak\Tontine\Validation\Tontine\GuestInviteValidator;

use function Jaxon\pm;

class Host extends Component
{
    /**
     * @var GuestInviteValidator
     */
    protected GuestInviteValidator $validator;

    /**
     * @param GuestService $guestService
     */
    public function __construct(private GuestService $guestService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.invite.host.home');
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
        $this->guestService->createInvite($values['email']);

        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.sent'));

        return $this->cl(HostPage::class)->page();
    }

    public function cancel(int $inviteId)
    {
        $this->guestService->cancelInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.cancelled'));

        return $this->cl(HostPage::class)->page();
    }

    public function delete(int $inviteId)
    {
        $this->guestService->deleteHostInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

        return $this->cl(HostPage::class)->page();
    }
}
