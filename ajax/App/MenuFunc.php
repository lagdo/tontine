<?php

namespace Ajax\App;

use Ajax\App\Admin\Guild\Guild;
use Ajax\App\Page\Sidebar\AdminMenu;
use Ajax\App\Page\Sidebar\RoundMenu;
use Ajax\App\Page\MainTitle;
use Ajax\App\Planning\Financial\Pool;
use Ajax\App\Guild\Member\Member;
use Ajax\FuncComponent;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\Planning\RoundService;

use function Jaxon\pm;
use function trans;

class MenuFunc extends FuncComponent
{
    /**
     * @param GuildService $guildService
     */
    public function __construct(protected GuildService $guildService,
        protected RoundService $roundService)
    {}

    public function admin()
    {
        $guild = $this->tenantService->guild();
        $this->tenantService->resetRound();
        $this->stash()->set('menu.current.guild', $guild);

        $this->response->jq('#header-menu-home')->hide();
        $this->cl(AdminMenu::class)->render();
        $this->cl(MainTitle::class)->render();
        $this->cl(Guild::class)->home();
    }

    public function showGuilds()
    {
        $guild = $this->tenantService->guild();
        $title = trans('tontine.titles.choose');
        $content = $this->renderView('pages.select.guild', [
            'current' => $guild?->id ?? 0,
            'guilds' => $this->guildService->getGuilds()->pluck('name', 'id'),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('tontine.actions.choose'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveGuild(pm()->select('guild_id')->toInt()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function saveGuild(int $guildId)
    {
        if(!($guild = $this->guildService->getUserOrGuestGuild($guildId)))
        {
            return;
        }

        $this->bag('tenant')->set('guild.id', $guild->id);
        $this->bag('tenant')->set('round.id', 0);
        $this->stash()->set('menu.current.guild', $guild);
        $this->tenantService->setGuild($guild);
        $this->tenantService->resetRound();

        $this->response->jq('#header-menu-home')->hide();
        $this->cl(MainTitle::class)->render();
        $this->cl(AdminMenu::class)->render();
        $this->cl(Member::class)->home();

        $this->modal()->hide();
        $this->alert()->info(trans('tontine.messages.selected', [
            'guild' => $guild->name,
        ]));
    }

    public function showRounds()
    {
        if(!($guild = $this->tenantService->guild()))
        {
            return;
        }

        $round = $this->tenantService->round();
        $title = trans('tontine.round.titles.choose');
        $content = $this->renderView('pages.select.round', [
            'current' => $round?->id ?? 0,
            'rounds' => $guild->rounds->pluck('title', 'id'),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('tontine.actions.choose'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRound(pm()->select('round_id')->toInt()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @databag planning
     */
    public function saveRound(int $roundId)
    {
        if(!($guild = $this->tenantService->guild()))
        {
            return;
        }
        if(!($round = $this->roundService->getRound($roundId)))
        {
            return;
        }
        if(!$this->checkHostAccess('planning', 'sessions', true))
        {
            return;
        }

        // Save the tontine and round ids in the user session.
        $this->bag('tenant')->set('guild.id', $round->guild->id);
        $this->bag('tenant')->set('round.id', $round->id);
        $this->tenantService->setRound($round);

        $this->response->jq('#header-menu-home')->show();
        $this->cl(RoundMenu::class)->render();
        $this->cl(MainTitle::class)->render();
        $this->cl(Pool::class)->home();

        $this->modal()->hide();
        $this->alert()->info(trans('tontine.round.messages.selected', [
            'guild' => $guild->name,
            'round' => $round->title,
        ]));
    }
}
