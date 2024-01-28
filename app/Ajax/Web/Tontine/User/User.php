<?php

namespace App\Ajax\Web\Tontine\User;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Tontine\GuestService;
use Siak\Tontine\Validation\Tontine\GuestInviteValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag invite
 */
class User extends CallableClass
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
        $this->jq('.btn-host-invite-access')->click($this->rq(Access::class)->home($inviteId));
        $this->jq('.btn-host-invite-cancel')->click($this->rq()->cancel($inviteId)
            ->confirm(trans('tontine.invite.questions.cancel')));
        $this->jq('.btn-host-invite-delete')->click($this->rq()->hostDelete($inviteId)
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
        $this->jq('.btn-guest-invite-accept')->click($this->rq()->accept($inviteId)
            ->confirm(trans('tontine.invite.questions.accept')));
        $this->jq('.btn-guest-invite-refuse')->click($this->rq()->refuse($inviteId)
            ->confirm(trans('tontine.invite.questions.refuse')));
        $this->jq('.btn-guest-invite-delete')->click($this->rq()->guestDelete($inviteId)
            ->confirm(trans('tontine.invite.questions.delete')));

        return $this->response;
    }

    public function add()
    {
        $title = trans('tontine.invite.titles.add');
        $content = $this->render('pages.invite.host.add');
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
        $this->notify->success(trans('tontine.invite.messages.sent'), trans('common.titles.success'));

        return $this->hosts();
    }

    public function accept(int $inviteId)
    {
        $this->guestService->acceptInvite($inviteId);
        $this->notify->success(trans('tontine.invite.messages.accepted'), trans('common.titles.success'));

        return $this->guests();
    }

    public function refuse(int $inviteId)
    {
        $this->guestService->refuseInvite($inviteId);
        $this->notify->success(trans('tontine.invite.messages.refused'), trans('common.titles.success'));

        return $this->guests();
    }

    public function cancel(int $inviteId)
    {
        $this->guestService->cancelInvite($inviteId);
        $this->notify->success(trans('tontine.invite.messages.cancelled'), trans('common.titles.success'));

        return $this->hosts();
    }

    public function hostDelete(int $inviteId)
    {
        $this->guestService->deleteHostInvite($inviteId);
        $this->notify->success(trans('tontine.invite.messages.deleted'), trans('common.titles.success'));

        return $this->hosts();
    }

    public function guestDelete(int $inviteId)
    {
        $this->guestService->deleteGuestInvite($inviteId);
        $this->notify->success(trans('tontine.invite.messages.deleted'), trans('common.titles.success'));

        return $this->guests();
    }
}
