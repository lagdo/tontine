<?php

namespace App\Ajax\Web\Tontine\User;

use App\Ajax\CallableClass;
use App\Ajax\Web\Tontine\User;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\GuestInvite as InviteModel;
use Siak\Tontine\Model\Tontine as TontineModel;
use Siak\Tontine\Service\Tontine\GuestService;

use Siak\Tontine\Service\Tontine\TontineService;
use function Jaxon\pm;
use function trans;

/**
 * @databag invite
 * @before getInvite
 */
class Access extends CallableClass
{
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
        if($this->target()->method() === 'home')
        {
            return;
        }
        $tontineId = $this->bag('invite')->get('tontine.id');
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
        $this->bag('invite')->set('invite.id', $this->invite->id);
        $this->bag('invite')->set('tontine.id', $this->tontine->id);

        $html = $this->render('pages.invite.access.home', [
            'guest' => $this->invite->guest,
        ]);
        $this->response->html('content-host-invites-home', $html);
        $this->jq('#btn-host-invites-back')->click($this->rq(User::class)->home());

        return $this->tontine();
    }

    public function tontine()
    {
        return $this->response;
    }
}
