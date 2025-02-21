<?php

namespace Ajax\App\Admin\User\Guest;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Tontine\UserService;

/**
 * @databag user
 */
class GuestFunc extends FuncComponent
{
    /**
     * @param UserService $userService
     */
    public function __construct(private UserService $userService)
    {}

    public function accept(int $inviteId)
    {
        $this->userService->acceptInvite($inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.accepted'));

        $this->cl(GuestPage::class)->page();
    }

    public function refuse(int $inviteId)
    {
        $this->userService->refuseInvite($inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.refused'));

        $this->cl(GuestPage::class)->page();
    }

    public function delete(int $inviteId)
    {
        if($this->userService->deleteGuestInvite($inviteId))
        {
            // The active tontine invite is deleted. Reload the page.
            $this->response->redirect('/');
            return;
        }

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

       $this->cl(GuestPage::class)->page();
    }
}
