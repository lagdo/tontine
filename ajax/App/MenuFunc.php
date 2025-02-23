<?php

namespace Ajax\App;

use Ajax\App\Admin\Organisation\Organisation;
use Ajax\App\Page\Sidebar\AdminMenu;
use Ajax\App\Page\Sidebar\RoundMenu;
use Ajax\App\Page\MainTitle;
use Ajax\App\Planning\Financial\Pool;
use Ajax\App\Tontine\Member\Member;
use Ajax\FuncComponent;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Tontine\TontineService;

use function Jaxon\pm;
use function trans;

class MenuFunc extends FuncComponent
{
    /**
     * @param TontineService $tontineService
     */
    public function __construct(protected TontineService $tontineService,
        protected RoundService $roundService)
    {}

    public function admin()
    {
        $tontine = $this->tenantService->tontine();
        $this->tenantService->resetRound();
        $this->stash()->set('menu.current.tontine', $tontine);

        $this->cl(AdminMenu::class)->render();
        $this->cl(MainTitle::class)->render();
        $this->cl(Organisation::class)->home();
    }

    public function showOrganisations()
    {
        $tontine = $this->tenantService->tontine();
        $title = trans('tontine.titles.choose');
        $content = $this->renderView('pages.select.tontine', [
            'current' => $tontine?->id ?? 0,
            'tontines' => $this->tontineService->getTontines()->pluck('name', 'id'),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('tontine.actions.choose'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveOrganisation(pm()->select('tontine_id')->toInt()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function saveOrganisation(int $tontineId)
    {
        if(!($tontine = $this->tontineService->getUserOrGuestTontine($tontineId)))
        {
            return;
        }

        $this->bag('tenant')->set('tontine.id', $tontine->id);
        $this->bag('tenant')->set('round.id', 0);
        $this->stash()->set('menu.current.tontine', $tontine);
        $this->tenantService->setTontine($tontine);
        $this->tenantService->resetRound();

        $this->cl(MainTitle::class)->render();
        $this->cl(AdminMenu::class)->render();
        $this->cl(Member::class)->home();

        $this->modal()->hide();
        $this->alert()->info(trans('tontine.messages.selected', [
            'tontine' => $tontine->name,
        ]));
    }

    public function showRounds()
    {
        if(!($tontine = $this->tenantService->tontine()))
        {
            return;
        }

        $round = $this->tenantService->round();
        $title = trans('tontine.round.titles.choose');
        $content = $this->renderView('pages.select.round', [
            'current' => $round?->id ?? 0,
            'rounds' => $tontine->rounds->pluck('title', 'id'),
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
        if(!($tontine = $this->tenantService->tontine()))
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
        $this->bag('tenant')->set('tontine.id', $round->tontine->id);
        $this->bag('tenant')->set('round.id', $round->id);
        $this->tenantService->setRound($round);

        $this->cl(RoundMenu::class)->render();
        $this->cl(MainTitle::class)->render();
        $this->cl(Pool::class)->home();

        $this->modal()->hide();
        $this->alert()->info(trans('tontine.round.messages.selected', [
            'tontine' => $tontine->name,
            'round' => $round->title,
        ]));
    }
}
