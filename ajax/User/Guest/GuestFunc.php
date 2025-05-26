<?php

namespace Ajax\User\Guest;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Guild\UserService;

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
        $user = $this->tenantService->user();
        $this->userService->acceptInvite($user, $inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.accepted'));

        $this->cl(GuestPage::class)->page();
    }

    public function refuse(int $inviteId)
    {
        $user = $this->tenantService->user();
        $this->userService->refuseInvite($user, $inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.refused'));

        $this->cl(GuestPage::class)->page();
    }

    public function delete(int $inviteId)
    {
        $guild = $this->tenantService->guild();
        if($this->userService->deleteGuestInvite($guild, $inviteId))
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
