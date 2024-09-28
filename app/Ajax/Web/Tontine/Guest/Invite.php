<?php

namespace App\Ajax\Web\Tontine\Guest;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Tontine\GuestService;
use Siak\Tontine\Validation\Tontine\GuestInviteValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag invite
 */
class Invite extends CallableClass
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
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.tontines'));
        $this->response->html('content-home', $this->renderView('pages.invite.home'));

        $this->response->js()->setSmScreenHandler('invites-sm-screens');

        $this->cl(Invite\HostPage::class)->page();
        $this->cl(Invite\GuestPage::class)->page();

        return $this->response;
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
        $this->notify->title(trans('common.titles.success'))->success(trans('tontine.invite.messages.sent'));

        return $this->cl(Invite\HostPage::class)->page();
    }

    public function accept(int $inviteId)
    {
        $this->guestService->acceptInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))->success(trans('tontine.invite.messages.accepted'));

        return $this->cl(Invite\GuestPage::class)->page();
    }

    public function refuse(int $inviteId)
    {
        $this->guestService->refuseInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))->success(trans('tontine.invite.messages.refused'));

        return $this->cl(Invite\GuestPage::class)->page();
    }

    public function cancel(int $inviteId)
    {
        $this->guestService->cancelInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))->success(trans('tontine.invite.messages.cancelled'));

        return $this->cl(Invite\HostPage::class)->page();
    }

    public function hostDelete(int $inviteId)
    {
        $this->guestService->deleteHostInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))->success(trans('tontine.invite.messages.deleted'));

        return $this->cl(Invite\HostPage::class)->page();
    }

    public function guestDelete(int $inviteId)
    {
        if($this->guestService->deleteGuestInvite($inviteId))
        {
            // The active tontine invite is deleted. Reload the page.
            $this->response->redirect('/');
            return $this->response;
        }

        $this->notify->title(trans('common.titles.success'))->success(trans('tontine.invite.messages.deleted'));
        return $this->cl(Invite\GuestPage::class)->page();
    }
}
