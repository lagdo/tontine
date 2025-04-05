<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Component;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Tontine\UserService;
use Siak\Tontine\Service\Tontine\TontineService;
use Stringable;

use function trans;

/**
 * @databag user
 * @before getInvite
 */
class Access extends Component
{
    /**
     * @var string
     */
    protected $overrides = Host::class;

    /**
     * @param UserService $userService
     * @param TontineService $tontineService
     */
    public function __construct(private UserService $userService,
        private TontineService $tontineService)
    {}

    protected function getInvite()
    {
        if($this->target()->method() === 'home')
        {
            $this->bag('user')->set('invite.id', $this->target()->args()[0]);
        }
        $inviteId = $this->bag('user')->get('invite.id');
        if(!($invite = $this->userService->getHostInvite($inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }
        $this->stash()->set('user.invite', $invite);

        // Do not find the tontine on the home page.
        if($this->target()->method() === 'home')
        {
            return;
        }

        $tontineId = $this->target()->method() === 'tontine' ? $this->target()->args()[0] :
            $this->bag('user')->get('tontine.id');
        $this->stash()->set('user.tontine', $this->tontineService->getTontine($tontineId));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $invite = $this->stash()->get('user.invite');

        return $this->renderView('pages.admin.user.host.access.home', [
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
            $this->alert()->title(trans('common.titles.warning'))
                ->warning(trans('tontine.invite.errors.tontines'));
            return;
        }

        $tontine = $tontines->first();
        $this->bag('user')->set('tontine.id', $tontine->id);
        $this->stash()->set('user.tontine', $tontine);

        $this->render();
    }
}
