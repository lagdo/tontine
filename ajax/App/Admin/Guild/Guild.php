<?php

namespace Ajax\App\Admin\Guild;

use Ajax\Component;
use Ajax\App\Admin\Guest\Guild as GuestGuild;
use Ajax\Page\SectionContent;
use Ajax\Page\SectionTitle;
use Siak\Tontine\Service\Guild\GuildService;
use Stringable;

use function trans;

/**
 * @databag admin
 */
class Guild extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @param GuildService $guildService
     */
    public function __construct(private GuildService $guildService)
    {}

    /**
     * @callback jaxon.ajax.callback.hideMenuOnMobile
     */
    public function home()
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.admin'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $user = $this->tenantService->user();
        return $this->renderView('pages.admin.guild.home', [
            'hasGuestGuilds' => $this->guildService->hasGuestGuilds($user),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $user = $this->tenantService->user();
        $this->cl(GuildPage::class)->page();
        if($this->guildService->hasGuestGuilds($user))
        {
            $this->cl(GuestGuild::class)->render();
        }
    }
}
