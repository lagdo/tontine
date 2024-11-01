<?php

namespace App\Ajax\Web\Tontine\Invite\Host;

use App\Ajax\Component;
use App\Ajax\Web\Tontine\Invite\Host;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Tontine\InviteService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\GuestAccessValidator;

use function trans;

/**
 * @databag invite
 * @before getInvite
 */
class Access extends Component
{
    /**
     * @var string
     */
    protected $overrides = Host::class;

    /**
     * @var GuestAccessValidator
     */
    protected GuestAccessValidator $validator;

    /**
     * @param InviteService $inviteService
     * @param TontineService $tontineService
     */
    public function __construct(private InviteService $inviteService,
        private TontineService $tontineService)
    {}

    protected function getInvite()
    {
        if($this->target()->method() === 'home')
        {
            $this->bag('invite')->set('invite.id', $this->target()->args()[0]);
        }
        $inviteId = $this->bag('invite')->get('invite.id');
        if(!($invite = $this->inviteService->getHostInvite($inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }
        $this->cache->set('invite.invite', $invite);

        // Do not find the tontine on the home page.
        if($this->target()->method() === 'home')
        {
            return;
        }

        $tontineId = $this->target()->method() === 'tontine' ? $this->target()->args()[0] :
            $this->bag('invite')->get('tontine.id');
        $this->cache->set('invite.tontine', $this->tontineService->getTontine($tontineId));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $invite = $this->cache->get('invite.invite');

        return (string)$this->renderView('pages.invite.guest.access.home', [
            'guest' => $invite->guest,
            'tontines' => $this->tenantService->user()->tontines->pluck('name', 'id'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(AccessContent::class)->render();
    }

    public function home(int $inviteId)
    {
        $tontines = $this->tenantService->user()->tontines;
        if($tontines->count() === 0)
        {
            $this->notify->title(trans('common.titles.warning'))
                ->warning(trans('tontine.invite.errors.tontines'));
            return $this->response;
        }

        $tontine = $tontines->first();
        $this->bag('invite')->set('tontine.id', $tontine->id);
        $this->cache->set('invite.tontine', $tontine);

        return $this->render();
    }

    public function tontine(int $tontineId)
    {
        $this->bag('invite')->set('tontine.id', $tontineId);

        return $this->cl(AccessContent::class)->render();
    }

    /**
     * @di $validator
     */
    public function saveAccess(array $formValues)
    {
        $invite = $this->cache->get('invite.invite');
        $tontine = $this->cache->get('invite.tontine');
        $access = $this->validator->validateItem($formValues['access'] ?? []);
        $this->inviteService->saveGuestTontineAccess($invite, $tontine, $access);

        $this->notify->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.saved'));

        return $this->cl(AccessContent::class)->render();
    }
}
