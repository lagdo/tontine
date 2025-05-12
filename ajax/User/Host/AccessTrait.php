<?php

namespace Ajax\User\Host;

use Siak\Tontine\Exception\MessageException;

use function trans;

trait AccessTrait
{
    protected function getInvite(): void
    {
        if($this->target()->method() === 'home')
        {
            $this->bag('user.access')->set('invite.id', $this->target()->args()[0]);
        }
        $inviteId = $this->bag('user.access')->get('invite.id');
        if(!($invite = $this->userService->getHostInvite($inviteId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }
        $this->stash()->set('user.access.invite', $invite);
    }

    protected function getGuild(): void
    {
        if($this->target()->method() === 'guild')
        {
            $this->bag('user.access')->set('guild.id', $this->target()->args()[0]);
        }

        $guildId = $this->bag('user.access')->get('guild.id');
        if(!($guild = $this->guildService->getGuild($guildId)))
        {
            throw new MessageException(trans('tontine.invite.errors.invite_not_found'));
        }
        $this->stash()->set('user.access.guild', $guild);
    }
}
