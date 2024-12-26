<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Component;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Tontine\UserService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\HostAccessValidator;
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
     * @var HostAccessValidator
     */
    protected HostAccessValidator $validator;

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
        $this->cache()->set('user.invite', $invite);

        // Do not find the tontine on the home page.
        if($this->target()->method() === 'home')
        {
            return;
        }

        $tontineId = $this->target()->method() === 'tontine' ? $this->target()->args()[0] :
            $this->bag('user')->get('tontine.id');
        $this->cache()->set('user.tontine', $this->tontineService->getTontine($tontineId));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $invite = $this->cache()->get('user.invite');

        return $this->renderView('pages.user.host.access.home', [
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
            return $this->response;
        }

        $tontine = $tontines->first();
        $this->bag('user')->set('tontine.id', $tontine->id);
        $this->cache()->set('user.tontine', $tontine);

        return $this->render();
    }

    public function tontine(int $tontineId)
    {
        $this->bag('user')->set('tontine.id', $tontineId);

        return $this->cl(AccessContent::class)->render();
    }

    /**
     * @di $validator
     */
    public function saveAccess(array $formValues)
    {
        $invite = $this->cache()->get('user.invite');
        $tontine = $this->cache()->get('user.tontine');
        $access = $this->validator->validateItem($formValues['access'] ?? []);
        $this->userService->saveHostTontineAccess($invite, $tontine, $access);

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.saved'));

        return $this->cl(AccessContent::class)->render();
    }
}
