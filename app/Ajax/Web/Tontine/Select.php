<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\SelectCallable;
use App\Ajax\Web\Planning\Session;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Tontine\TontineService;

use function Jaxon\pm;
use function session;
use function trans;

/**
 * @databag tontine
 */
class Select extends SelectCallable
{
    /**
     * @param TontineService $tontineService
     */
    public function __construct(protected TontineService $tontineService,
        protected RoundService $roundService)
    {}

    public function showTontines()
    {
        $title = trans('tontine.titles.choose');
        $content = $this->renderView('pages.select.tontine')
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
        if(!($tontine = $this->tontineService->getUserOrGuestTontine($tontineId)))
        {
            return $this->response;
        }

        $this->bag('tenant')->set('tontine.id', $tontine->id);
        $this->bag('tenant')->set('round.id', 0);
        $this->tenantService->setTontine($tontine);

        $this->selectTontine($tontine);

        if(($round = $tontine->rounds->first()))
        {
            return $this->saveRound($round->id);
        }

        $this->dialog->hide();
        $this->notify->info(trans('tontine.messages.selected',
            ['tontine' => $tontine->name]));

        return $this->response;
    }

    public function showRounds()
    {
        if(!($tontine = $this->tenantService->tontine()))
        {
            return $this->response;
        }
        $title = trans('tontine.round.titles.choose');
        $content = $this->renderView('pages.select.round')
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

    /**
     * @databag planning
     */
    public function saveRound(int $roundId)
    {
        if(!($tontine = $this->tenantService->tontine()))
        {
            return $this->response;
        }
        if(!($round = $this->roundService->getRound($roundId)))
        {
            return $this->response;
        }

        $this->dialog->hide();

        // Save the tontine and round ids in the user session.
        $this->bag('tenant')->set('tontine.id', $tontine->id);
        $this->bag('tenant')->set('round.id', $round->id);
        $this->tenantService->setRound($round);

        $this->selectRound($round);
        // Update the session list.
        $this->cl(Session::class)->show($round);

        $this->notify->info(trans('tontine.round.messages.selected',
            ['tontine' => $tontine->name, 'round' => $round->title]));

        return $this->response;
    }
}
