<?php

namespace Ajax\User\Guest;

use Ajax\FuncComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\UserService;

#[Databag('user')]
class GuestFunc extends FuncComponent
{
    /**
     * @param UserService $userService
     */
    public function __construct(private UserService $userService)
    {}

    public function accept(int $inviteId): void
    {
        $user = $this->tenantService->user();
        $this->userService->acceptInvite($user, $inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.accepted'));

        $this->cl(GuestPage::class)->page();
    }

    public function refuse(int $inviteId): void
    {
        $user = $this->tenantService->user();
        $this->userService->refuseInvite($user, $inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.refused'));

        $this->cl(GuestPage::class)->page();
    }

    public function delete(int $inviteId): void
    {
        $guild = $this->tenantService->guild();
        $reloadPage = $this->userService->guildBelongsToInvite($guild, $inviteId);

        $user = $this->tenantService->user();
        $this->userService->deleteGuestInvite($user, $inviteId);

        if($reloadPage)
        {
            // The active guild invite is deleted. Reload the page.
            $this->response->redirect('/');
            return;
        }

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

        $this->cl(GuestPage::class)->page();
    }
}
