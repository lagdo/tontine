<?php

namespace Ajax\Page\Header;

use Ajax\Base;
use Ajax\Page\Sidebar\AdminMenu;
use Ajax\Page\Sidebar\GuildMenu;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Model\Guild as GuildModel;

use function Jaxon\select;
use function trans;
use function view;

class GuildMenuFunc extends Base\FuncComponent
{
    use Base\Guild\ComponentTrait;

    public function showGuilds(): void
    {
        $title = trans('tontine.titles.choose');
        $content = $this->renderTpl('parts.header.select.guild', [
            'current' => $this->tenantService->getLatestGuildId(),
            'guilds' => $this->tenantService->getGuilds(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ], [
            'title' => trans('tontine.actions.choose'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->selectGuild(select('guild_id')->toInt()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @param GuildModel $guild
     *
     * @return void
     */
    #[Exclude]
    public function setCurrentGuild(GuildModel $guild): void
    {
        $this->tenantService->setGuild($guild);

        $this->bag('tenant')->set('guild.id', $guild->id);
        $this->stash()->set('tenant.guild', $guild);

        view()->share('currentGuild', $guild);

        $this->cl(GuildHeader::class)->render();
        $this->cl(GuildMenu::class)->render();
        $this->cl(SectionHeader::class)->currency();
    }

    public function selectGuild(int $guildId): void
    {
        if(!($guild = $this->tenantService->getGuild($guildId)))
        {
            // Todo: show an error message.
            return;
        }

        $this->setCurrentGuild($guild);

        $this->modal()->hide();
        $this->alert()->info(trans('tontine.messages.selected', [
            'guild' => $guild->name,
        ]));
    }

    /**
     * @return void
     */
    private function resetCurrentGuild(): void
    {
        $this->bag('tenant')->set('guild.id', 0);
        $this->stash()->set('tenant.guild', null);

        view()->share('currentGuild', null);
    }

    /**
     * Go back to the Admin section.
     *
     * @return void
     */
    #[Before('getCurrentGuild')]
    public function back(): void
    {
        // Take the current guild before resetting the values.
        $guild = $this->stash()->get('tenant.guild');
        $this->resetCurrentGuild();

        $this->cl(GuildHeader::class)->render();
        $this->cl(AdminMenu::class)->render();
        $this->cl(SectionHeader::class)->currency();

        if($guild !== null)
        {
            $this->alert()->info(trans('tontine.messages.back_to_admin', [
                'guild' => $guild->name,
            ]));
        }
    }
}
