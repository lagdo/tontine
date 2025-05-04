<?php

namespace Ajax\User\Host;

use Ajax\Component;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\Guild\UserService;
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

        $guildId = $this->bag('user')->get('guild.id');
        $this->stash()->set('user.guild', $this->guildService->getGuild($guildId));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $invite = $this->stash()->get('user.invite');

        return $this->renderView('pages.admin.user.host.access.home', [
            'guest' => $invite->guest,
            'guilds' => $this->tenantService->user()->guilds->pluck('name', 'id'),
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
        $guilds = $this->tenantService->user()->guilds;
        if($guilds->count() === 0)
        {
            $this->alert()->title(trans('common.titles.warning'))
                ->warning(trans('tontine.invite.errors.no_guild'));
            return;
        }

        $guild = $guilds->first();
        $this->bag('user')->set('guild.id', $guild->id);
        $this->stash()->set('user.guild', $guild);

        $this->render();
    }
}
