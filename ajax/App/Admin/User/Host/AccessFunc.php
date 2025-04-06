<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\FuncComponent;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Tontine\GuildService;
use Siak\Tontine\Service\Tontine\UserService;
use Siak\Tontine\Validation\Guild\HostAccessValidator;

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
     * @param GuildService $guildService
     */
    public function __construct(private UserService $userService,
        private GuildService $guildService)
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

        // Do not find the invite on the home page.
        if($this->target()->method() === 'home')
        {
            return;
        }

        $guildId = $this->target()->method() === 'guild' ? $this->target()->args()[0] :
            $this->bag('user')->get('guild.id');
        $this->stash()->set('user.guild', $this->guildService->getGuild($guildId));
    }

    public function guild(int $guildId)
    {
        $this->bag('user')->set('guild.id', $guildId);

        $this->cl(AccessContent::class)->render();
    }

    /**
     * @di $validator
     */
    public function saveAccess(array $formValues)
    {
        $invite = $this->stash()->get('user.invite');
        $guild = $this->stash()->get('user.guild');
        $access = $this->validator->validateItem($formValues['access'] ?? []);
        $this->userService->saveHostGuildAccess($invite, $guild, $access);

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.saved'));

        $this->cl(AccessContent::class)->render();
    }
}
