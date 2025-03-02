<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\FuncComponent;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Tontine\UserService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\HostAccessValidator;

use function trans;

/**
 * @databag user
 * @before getInvite
 */
class AccessFunc extends FuncComponent
{
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

    public function tontine(int $tontineId)
    {
        $this->bag('user')->set('tontine.id', $tontineId);

        $this->cl(AccessContent::class)->render();
    }

    /**
     * @di $validator
     */
    public function saveAccess(array $formValues)
    {
        $invite = $this->stash()->get('user.invite');
        $tontine = $this->stash()->get('user.tontine');
        $access = $this->validator->validateItem($formValues['access'] ?? []);
        $this->userService->saveHostTontineAccess($invite, $tontine, $access);

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.saved'));

        $this->cl(AccessContent::class)->render();
    }
}
