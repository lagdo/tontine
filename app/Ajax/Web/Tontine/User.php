<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Tontine\GuestService;
use Siak\Tontine\Validation\Tontine\InviteValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag invite
 */
class User extends CallableClass
{
    /**
     * @var InviteValidator
     */
    protected InviteValidator $validator;

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
        $this->response->html('content-home', $this->render('pages.invite.home'));

        $this->jq('#btn-host-invite-create')->click($this->rq()->add());
        $this->jq('#btn-host-invites-refresh')->click($this->rq()->hosts());
        $this->jq('#btn-guest-invites-refresh')->click($this->rq()->guests());

        $this->hosts();
        $this->guests();

        return $this->response;
    }

    public function hosts(int $pageNumber = 0)
    {
        $inviteCount = $this->guestService->getHostInviteCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $inviteCount, 'invite', 'host.page');
        $invites = $this->guestService->getHostInvites($pageNumber);
        $pagination = $this->rq()->hosts()->paginate($pageNumber, $perPage, $inviteCount);

        $html = $this->render('pages.invite.host.page', [
            'invites' => $invites,
            'pagination' => $pagination,
        ]);
        $this->response->html('content-host-invites-page', $html);

        $inviteId = jq()->parent()->attr('data-invite-id')->toInt();
        $this->jq('.btn-invite-edit')->click($this->rq()->edit($inviteId));
        $this->jq('.btn-invite-toggle')->click($this->rq()->toggle($inviteId));
        $this->jq('.btn-invite-delete')->click($this->rq()->delete($inviteId)
            ->confirm(trans('tontine.invite.questions.delete')));

        return $this->response;
    }

    public function guests(int $pageNumber = 0)
    {
        $inviteCount = $this->guestService->getGuestInviteCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $inviteCount, 'invite', 'guest.page');
        $invites = $this->guestService->getGuestInvites($pageNumber);
        $pagination = $this->rq()->guests()->paginate($pageNumber, $perPage, $inviteCount);

        $html = $this->render('pages.invite.guest.page', [
            'invites' => $invites,
            'pagination' => $pagination,
        ]);
        $this->response->html('content-guest-invites-page', $html);

        $inviteId = jq()->parent()->attr('data-invite-id')->toInt();
        $this->jq('.btn-invite-edit')->click($this->rq()->edit($inviteId));
        $this->jq('.btn-invite-toggle')->click($this->rq()->toggle($inviteId));
        $this->jq('.btn-invite-delete')->click($this->rq()->delete($inviteId)
            ->confirm(trans('tontine.invite.questions.delete')));

        return $this->response;
    }
}
