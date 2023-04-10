<?php

namespace App\Ajax\App\Tontine;

use App\Ajax\App\Meeting\Meeting;
use App\Ajax\App\Meeting\Member;
use App\Ajax\App\Meeting\Report as MeetingReport;
use App\Ajax\App\Planning\Planning;
use App\Ajax\App\Planning\Pool;
use App\Ajax\App\Planning\Report as PlanningReport;
use App\Ajax\App\Planning\Session;
use App\Ajax\CallableClass;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TontineService;

use function Jaxon\pm;
use function session;
use function trans;

/**
 * @databag tontine
 */
class Select extends CallableClass
{
    /**
     * @di
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @di
     * @var TontineService
     */
    protected TontineService $tontineService;

    /**
     * @var MemberService
     */
    protected MemberService $memberService;

    /**
     * @exclude
     */
    public function resetSidebarMenu()
    {
        $this->response->html('sidebar-menu-tontine', $this->view()->render('tontine.parts.sidebar.tontine'));
        $this->response->html('sidebar-menu-round', $this->view()->render('tontine.parts.sidebar.round'));
    }

    public function show()
    {
        return $this->showTontine();
    }

    private function showTontine()
    {
        $title = trans('tontine.titles.choose');
        $content = $this->view()->render('tontine.pages.select.tontine')
            ->with('default', session('tontine.id', 0))
            ->with('tontines', $this->tontineService->getTontines()->pluck('name', 'id'));
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveTontine(pm()->select('tontine_id')->toInt()),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    /**
     * @di $memberService
     */
    public function saveTontine(int $tontineId)
    {
        $tontine = $this->tontineService->getTontine($tontineId);
        if(!$tontine)
        {
            return $this->response;
        }

        session(['tontine.id' => $tontine->id, 'round.id' => 0]);
        $this->bag('tontine')->set('tontine.id', $tontine->id);
        $this->tenantService->setTontine($tontine);

        $this->response->html('section-tontine-name', $tontine->name);

        // Set the tontine sidebar menu
        $this->response->html('sidebar-menu-tontine', $this->view()->render('tontine.parts.sidebar.tontine'));
        $this->jq('a', '#sidebar-menu-tontine')->css('color', '#6777ef');

        $this->jq('#tontine-menu-members')->click($this->cl(Member::class)->rq()->home());
        $this->jq('#tontine-menu-charges')->click($this->cl(Charge::class)->rq()->home());

        // Reset the round sidebar menu
        $this->response->html('sidebar-menu-round', $this->view()->render('tontine.parts.sidebar.round'));

        $this->dialog->hide();

        return $this->showRound();
    }

    private function showRound()
    {
        $title = trans('tontine.round.titles.choose');
        $content = $this->view()->render('tontine.pages.select.round')
            ->with('rounds', $this->tontineService->getRounds()->pluck('title', 'id'));
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRound(pm()->select('round_id')->toInt()),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    public function saveRound(int $roundId)
    {
        $round = $this->tontineService->getRound($roundId);
        if(!$round)
        {
            return $this->response;
        }
        $tontine = $this->tenantService->tontine();

        // Save the tontine and round ids in the user session.
        session(['tontine.id' => $tontine->id, 'round.id' => $round->id]);
        $this->tenantService->setRound($round);

        $this->response->html('section-tontine-name', $tontine->name . ' - ' . $round->title);

        // Set the round sidebar menu
        $this->response->html('sidebar-menu-round', $this->view()->render('tontine.parts.sidebar.round'));
        $this->jq('a', '#sidebar-menu-round')->css('color', '#6777ef');

        $this->jq('#planning-menu-subscriptions')->click($this->cl(Pool::class)->rq()->home());
        $this->jq('#planning-menu-sessions')->click($this->cl(Session::class)->rq()->home());
        $this->jq('#planning-menu-beneficiaries')->click($this->cl(Planning::class)->rq()->beneficiaries());
        $this->jq('#planning-menu-reports')->click($this->cl(PlanningReport::class)->rq()->home());
        $this->jq('#meeting-menu-sessions')->click($this->cl(Meeting::class)->rq()->home());
        $this->jq('#meeting-menu-members')->click($this->cl(Member::class)->rq()->home());
        $this->jq('#meeting-menu-reports')->click($this->cl(MeetingReport::class)->rq()->home());

        $this->dialog->hide();

        return $this->response;
    }
}
