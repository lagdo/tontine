<?php

namespace Ajax\Page;

use Ajax\App\Admin\Guild\Guild;
use Ajax\App\Planning\Participation;
use Ajax\FuncComponent;
use Ajax\Page\Sidebar\AdminMenu;
use Ajax\Page\Sidebar\RoundMenu;
use Ajax\Page\MainTitle;
use Siak\Tontine\Model\Guild as GuildModel;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\Guild\RoundService;

use function je;
use function trans;

class MenuFunc extends FuncComponent
{
    /**
     * @param GuildService $guildService
     */
    public function __construct(protected GuildService $guildService,
        protected RoundService $roundService)
    {}

    public function admin(): void
    {
        $guild = $this->tenantService->guild();
        $this->tenantService->resetRound();
        $this->stash()->set('menu.current.guild', $guild);

        $this->response->jq('#header-menu-home')->hide();
        $this->cl(AdminMenu::class)->render();
        $this->cl(MainTitle::class)->render();
        $this->cl(Guild::class)->home();

        if(!$guild)
        {
            return;
        }
        $this->alert()->info(trans('tontine.messages.back_to_admin', [
            'guild' => $guild->name,
        ]));
    }

    public function showGuilds(): void
    {
        $user = $this->tenantService->user();
        $guild = $this->tenantService->guild();
        $title = trans('tontine.titles.choose');
        $content = $this->renderView('pages.select.guild', [
            'current' => $guild?->id ?? 0,
            'guilds' => $this->guildService->getGuilds($user)->pluck('name', 'id'),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('tontine.actions.choose'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveGuild(je('guild_id')->rd()->select()->toInt()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @exclude
     * @param GuildModel $guild
     *
     * @return void
     */
    public function setCurrentGuild(GuildModel $guild): void
    {
        $this->bag('tenant')->set('guild.id', $guild->id);
        $this->bag('tenant')->set('round.id', 0);
        $this->stash()->set('tenant.guild', $guild);
        $this->stash()->set('tenant.round', null);

        // This one is used to set the sidebar menu content.
        $this->stash()->set('menu.current.guild', $guild);

        $this->tenantService->setGuild($guild);
    }

    public function saveGuild(int $guildId): void
    {
        $user = $this->tenantService->user();
        if(!($guild = $this->guildService->getUserOrGuestGuild($user, $guildId)))
        {
            return;
        }
        if($guild->id === $this->tenantService->guild()->id)
        {
            $this->modal()->hide();
            return; // No change.
        }

        $this->setCurrentGuild($guild);

        $this->response->jq('#header-menu-home')->hide();
        $this->cl(MainTitle::class)->render();
        $this->cl(AdminMenu::class)->render();
        $this->cl(Guild::class)->home();

        $this->modal()->hide();
        $this->alert()->info(trans('tontine.messages.selected', [
            'guild' => $guild->name,
        ]));
    }

    public function showRounds(): void
    {
        if(!($guild = $this->tenantService->guild()))
        {
            return;
        }

        $round = $this->tenantService->round();
        $title = trans('tontine.round.titles.choose');
        $content = $this->renderView('pages.select.round', [
            'current' => $round?->id ?? 0,
            'rounds' => $this->roundService->getRoundList($guild),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('tontine.actions.choose'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRound(je('round_id')->rd()->select()->toInt()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @databag planning
     */
    public function saveRound(int $roundId): void
    {
        if(!($guild = $this->tenantService->guild()))
        {
            return;
        }
        if(!($round = $this->roundService->getRound($guild, $roundId)) ||
            $this->roundService->getSessionCount($round) === 0)
        {
            return;
        }
        // Throws an exception.
        $this->checkHostAccess('planning', 'finance');

        // Save the tontine and round ids in the user session.
        $this->bag('tenant')->set('guild.id', $round->guild->id);
        $this->bag('tenant')->set('round.id', $round->id);
        $this->stash()->set('tenant.guild', $guild);
        $this->stash()->set('tenant.round', $round);

        $this->tenantService->setRound($round);

        $this->response->jq('#header-menu-home')->show();
        $this->cl(RoundMenu::class)->render();
        $this->cl(MainTitle::class)->render();
        $this->cl(Participation::class)->home();

        $this->modal()->hide();
        $this->alert()->info(trans('tontine.round.messages.selected', [
            'guild' => $guild->name,
            'round' => $round->title,
        ]));
    }
}
