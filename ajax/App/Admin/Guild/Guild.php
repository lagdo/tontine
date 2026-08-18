<?php

namespace Ajax\App\Admin\Guild;

use Ajax\App\Admin\Guest\Guild as GuestGuild;
use Ajax\Base\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Guild\GuildService;

#[Databag('admin')]
class Guild extends Component
{
    /**
     * @param GuildService $guildService
     */
    public function __construct(private GuildService $guildService)
    {}

    /**
     * @return string
     */
    protected function overrides(): string
    {
        return SectionContent::class;
    }

    #[Before('setSectionTitle', ["admin", "guilds"])]
    #[Callback('tontine.hideMenu')]
    public function home()
    {
        $this->render();
    }

    /**
     * @return void
     */
    private function hasGuestGuilds(): bool
    {
        return $this->guildService->hasGuestGuilds($this->tenantService->user());
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderTpl('pages.admin.guild.home', [
            'hasGuestGuilds' => $this->hasGuestGuilds(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->cl(GuildPage::class)->page();

        if($this->hasGuestGuilds())
        {
            $this->cl(GuestGuild::class)->render();
        }
    }
}
