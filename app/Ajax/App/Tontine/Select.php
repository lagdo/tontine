<?php

namespace App\Ajax\App\Tontine;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\TenantService;
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
            'title' => trans('tontine.actions.choose'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveTontine(pm()->select('tontine_id')->toInt()),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function saveTontine(int $tontineId)
    {
        if(!($tontine = $this->tontineService->getTontine($tontineId)))
        {
            return $this->response;
        }

        session(['tontine.id' => $tontine->id, 'round.id' => 0]);
        $this->tenantService->setTontine($tontine);

        $this->selectTontine($tontine);

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
            'title' => trans('tontine.actions.choose'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRound(pm()->select('round_id')->toInt()),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function saveRound(int $roundId)
    {
        if(!($round = $this->tontineService->getRound($roundId)))
        {
            return $this->response;
        }
        $tontine = $this->tenantService->tontine();

        // Save the tontine and round ids in the user session.
        session(['tontine.id' => $tontine->id, 'round.id' => $round->id]);
        $this->tenantService->setRound($round);

        $this->selectRound($round);

        $this->dialog->hide();

        return $this->response;
    }
}
