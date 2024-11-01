<?php

namespace App\Ajax\Web\Tontine\Invite;

use App\Ajax\Component;
use Siak\Tontine\Service\Tontine\GuestService;

class Guest extends Component
{
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
        return (string)$this->renderView('pages.invite.guest.home');
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
        $this->guestService->acceptInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.accepted'));

        return $this->cl(GuestPage::class)->page();
    }

    public function refuse(int $inviteId)
    {
        $this->guestService->refuseInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.refused'));

        return $this->cl(GuestPage::class)->page();
    }

    public function delete(int $inviteId)
    {
        if($this->guestService->deleteGuestInvite($inviteId))
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
