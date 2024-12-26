<?php

namespace Ajax\App\Admin\User\Guest;

use Ajax\Component;
use Siak\Tontine\Service\Tontine\UserService;
use Stringable;

/**
 * @databag user
 */
class Guest extends Component
{
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
        return $this->renderView('pages.user.guest.home');
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
        $this->userService->acceptInvite($inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.accepted'));

        return $this->cl(GuestPage::class)->page();
    }

    public function refuse(int $inviteId)
    {
        $this->userService->refuseInvite($inviteId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.refused'));

        return $this->cl(GuestPage::class)->page();
    }

    public function delete(int $inviteId)
    {
        if($this->userService->deleteGuestInvite($inviteId))
        {
            // The active tontine invite is deleted. Reload the page.
            $this->response->redirect('/');
            return $this->response;
        }

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

        return $this->cl(GuestPage::class)->page();
    }
}
