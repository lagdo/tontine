<?php

namespace App\Ajax\Web\Tontine\User;

use App\Ajax\CallableClass;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\GuestInvite as InviteModel;
use Siak\Tontine\Model\Tontine as TontineModel;
use Siak\Tontine\Service\Tontine\GuestService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\GuestAccessValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag invite
 * @before getInvite
 */
class Access extends CallableClass
{
    /**
     * @var GuestAccessValidator
     */
    protected GuestAccessValidator $validator;

    /**
     * @var InviteModel
     */
    private $invite;

    /**
     * @var TontineModel
     */
    private $tontine;

    /**
     * @param GuestService $guestService
     */
    public function __construct(private GuestService $guestService,
        private TontineService $tontineService)
    {}

    protected function getInvite()
    {
        $inviteId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('invite')->get('invite.id');
        if(!($this->invite = $this->guestService->getHostInvite($inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }
        // Do not find the tontine on the home page.
        if($this->target()->method() === 'home')
        {
            return;
        }
        $tontineId = $this->target()->method() === 'tontine' ?
            $this->target()->args()[0] : $this->bag('invite')->get('tontine.id');
        $this->tontine = $this->tontineService->getTontine($tontineId);
    }

    public function home(int $inviteId)
    {
        $tontines = $this->tenantService->user()->tontines;
        if($tontines->count() === 0)
        {
            $this->notify->warning(trans('tontine.invite.errors.tontines'), trans('common.titles.warning'));
            return $this->response;
        }
        $this->tontine = $tontines->first();
        $this->bag('invite')->set('invite.id', $inviteId);
        $this->bag('invite')->set('tontine.id', $this->tontine->id);

        $html = $this->render('pages.invite.access.home', [
            'guest' => $this->invite->guest,
            'tontines' => $tontines->pluck('name', 'id'),
        ]);
        $this->response->html('content-host-invites-home', $html);
        $this->jq('#btn-host-invites-back')->click($this->rq(User::class)->home());
        $tontineId = pm()->select('select-invite-tontine');
        $this->jq('#btn-select-invite-tontine')->click($this->rq()->tontine($tontineId));

        return $this->access();
    }

    public function tontine(int $tontineId)
    {
        $this->bag('invite')->set('tontine.id', $tontineId);

        return $this->access();
    }

    private function access()
    {
        $access = $this->guestService->getGuestTontineAccess($this->invite, $this->tontine);
        $html = $this->render('pages.invite.access.tontine', [
            'tontine' => $this->tontine,
            'access' => $access,
        ]);
        $this->response->html('content-host-invite-access', $html);
        $this->jq('#btn-save-guest-tontine-access')
            ->click($this->rq()->saveAccess(pm()->form('guest-tontine-access-form')));

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveAccess(array $formValues)
    {
        $access = $this->validator->validateItem($formValues['access'] ?? []);
        $this->guestService->saveGuestTontineAccess($this->invite, $this->tontine, $access);

        $this->notify->success(trans('meeting.messages.saved'), trans('common.titles.success'));

        return $this->access();
    }
}
