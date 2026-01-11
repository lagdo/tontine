<?php

namespace Ajax\App\Admin\Guild;

use Ajax\Component;
use Ajax\App\Admin\Guest\Guild as GuestGuild;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\GuildService;
use Stringable;

#[Databag('admin')]
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

    #[Before('setSectionTitle', ["admin", "guilds"])]
    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
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
    protected function after(): void
    {
        $user = $this->tenantService->user();
        $this->cl(GuildPage::class)->page();
        if($this->guildService->hasGuestGuilds($user))
        {
            $this->cl(GuestGuild::class)->render();
        }
    }
}
