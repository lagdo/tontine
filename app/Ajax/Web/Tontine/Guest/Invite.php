<?php

namespace App\Ajax\Web\Tontine\Guest;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Tontine\GuestService;
use Siak\Tontine\Validation\Tontine\GuestInviteValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag invite
 */
class Invite extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

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
    public function home(): ComponentResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontines'));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.invite.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->setSmScreenHandler('invites-sm-screens');

        $this->cl(Invite\HostPage::class)->page();
        $this->cl(Invite\GuestPage::class)->page();
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

        return $this->cl(Invite\HostPage::class)->page();
    }

    public function accept(int $inviteId)
    {
        $this->guestService->acceptInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.accepted'));

        return $this->cl(Invite\GuestPage::class)->page();
    }

    public function refuse(int $inviteId)
    {
        $this->guestService->refuseInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.refused'));

        return $this->cl(Invite\GuestPage::class)->page();
    }

    public function cancel(int $inviteId)
    {
        $this->guestService->cancelInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.cancelled'));

        return $this->cl(Invite\HostPage::class)->page();
    }

    public function hostDelete(int $inviteId)
    {
        $this->guestService->deleteHostInvite($inviteId);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

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

        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.invite.messages.deleted'));

        return $this->cl(Invite\GuestPage::class)->page();
    }
}
