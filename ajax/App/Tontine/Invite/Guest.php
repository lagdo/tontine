<?php

namespace Ajax\App\Tontine\Invite;

use Ajax\Component;
use Siak\Tontine\Service\Tontine\InviteService;

class Guest extends Component
{
    /**
     * @param InviteService $inviteService
     */
    public function __construct(private InviteService $inviteService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.invite.guest.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(GuestPage::class)->page();
    }

    public function accept(int $inviteId)
    {
        $this->inviteService->acceptInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.accepted'));

        return $this->cl(GuestPage::class)->page();
    }

    public function refuse(int $inviteId)
    {
        $this->inviteService->refuseInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.refused'));

        return $this->cl(GuestPage::class)->page();
    }

    public function delete(int $inviteId)
    {
        if($this->inviteService->deleteGuestInvite($inviteId))
        {
            // The active tontine invite is deleted. Reload the page.
            $this->response->redirect('/');
            return $this->response;
        }

        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

        return $this->cl(GuestPage::class)->page();
    }
}
