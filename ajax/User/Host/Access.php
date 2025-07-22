<?php

namespace Ajax\User\Host;

use Ajax\Component;
use Siak\Tontine\Service\Guild\UserService;
use Stringable;

use function trans;

/**
 * @databag user.access
 * @before getInvite
 */
class Access extends Component
{
    use AccessTrait;

    /**
     * @var string
     */
    protected $overrides = Host::class;

    /**
     * @param UserService $userService
     */
    public function __construct(private UserService $userService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $invite = $this->stash()->get('user.access.invite');
        return $this->renderView('pages.admin.user.host.access.home', [
            'guest' => $invite->guest,
            'guilds' => $this->tenantService->user()->guilds->pluck('name', 'id'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $guild = $this->tenantService->user()->guilds->first();
        $this->bag('user.access')->set('guild.id', $guild->id);
        $this->stash()->set('user.access.guild', $guild);

        $this->cl(GuildAccess::class)->render();
    }

    public function home(int $inviteId): void
    {
        if($this->tenantService->user()->guilds->count() > 0)
        {
            $this->render();
            return;
        }

        $this->alert()->title(trans('common.titles.warning'))
            ->warning(trans('tontine.invite.errors.no_guild'));
    }
}
