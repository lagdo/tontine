<?php

namespace App\Ajax\Web\Tontine;

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

    public function showTontines()
    {
        $title = trans('tontine.titles.choose');
        $content = $this->render('pages.select.tontine')
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

        if($tontine->rounds->count() === 0)
        {
            $this->dialog->hide();
            $this->notify->info(trans('tontine.messages.selected', ['tontine' => $tontine->name]));

            return $this->response;
        }

        return $this->saveRound($tontine->rounds->first()->id);
    }

    public function showRounds()
    {
        if(!($tontine = $this->tenantService->tontine()))
        {
            return $this->response;
        }
        $title = trans('tontine.round.titles.choose');
        $content = $this->render('pages.select.round')
            ->with('rounds', $tontine->rounds->pluck('title', 'id'));
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRound(pm()->select('round_id')->toInt()),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function saveRound(int $roundId)
    {
        if(!($tontine = $this->tenantService->tontine()))
        {
            return $this->response;
        }
        if(!($round = $this->tontineService->getRound($roundId)))
        {
            return $this->response;
        }

        // Save the tontine and round ids in the user session.
        session(['tontine.id' => $tontine->id, 'round.id' => $round->id]);
        $this->tenantService->setRound($round);

        $this->selectRound($round);

        $this->dialog->hide();
        $this->notify->info(trans('tontine.round.messages.selected',
            ['tontine' => $tontine->name, 'round' => $round->title]));

        return $this->response;
    }
}
