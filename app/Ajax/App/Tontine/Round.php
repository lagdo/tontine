<?php

namespace App\Ajax\App\Tontine;

use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Tontine\PoolService;
use Siak\Tontine\Service\TenantService;
use App\Ajax\App\Meeting\Meeting;
use App\Ajax\App\Meeting\Report as MeetingReport;
use App\Ajax\App\Planning\Planning;
use App\Ajax\App\Planning\Pool;
use App\Ajax\App\Planning\Report as PlanningReport;
use App\Ajax\App\Planning\Session;
use App\Ajax\CallableClass;

use function intval;
use function Jaxon\jq;
use function Jaxon\pm;
use function session;
use function trans;

/**
 * @databag tontine
 * @before getTontine
 */
class Round extends CallableClass
{
    /**
     * @di
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @di
     * @var RoundService
     */
    protected RoundService $roundService;

    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @return void
     */
    protected function getTontine()
    {
        $tontineId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('tontine')->get('tontine.id');
        $this->tontine = $this->roundService->getTontine(intval($tontineId));
        $this->tenantService->setTontine($this->tontine);
    }

    /**
     * @exclude
     */
    public function show($tontine, $roundService)
    {
        $this->tontine = $tontine;
        $this->roundService = $roundService;

        return $this->home(0); // The parameter here is not relevant
    }

    public function home(int $tontineId)
    {
        $this->bag('tontine')->set('tontine.id', $this->tontine->id);
        $html = $this->view()->render('tontine.pages.round.home')->with('tontine', $this->tontine);
        $this->response->html('content-home', $html);

        $this->jq('#btn-round-create')->click($this->rq()->add());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('tontine')->get('round.page', 1);
        }
        $this->bag('tontine')->set('round.page', $pageNumber);

        $rounds = $this->roundService->getRounds($pageNumber);
        $roundCount = $this->roundService->getRoundCount();

        $html = $this->view()->render('tontine.pages.round.page')
            ->with('rounds', $rounds)
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $roundCount));
        $this->response->html('round-page', $html);

        $roundId = jq()->parent()->attr('data-round-id')->toInt();
        $this->jq('.btn-round-edit')->click($this->rq()->edit($roundId));
        // $this->jq('.btn-round-open')->click($this->rq()->open($roundId)
        //     ->confirm(trans('tontine.round.questions.open')));

        return $this->response;
    }

    public function add()
    {
        $title = trans('tontine.round.titles.add');
        $content = $this->view()->render('tontine.pages.round.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('round-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    public function create(array $formValues)
    {
        $this->roundService->createRound($formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.round.messages.created'), trans('common.titles.success'));

        return $this->response;
    }

    public function edit(int $roundId)
    {
        $round = $this->roundService->getRound($roundId);

        $title = trans('tontine.round.titles.edit');
        $content = $this->view()->render('tontine.pages.round.edit')->with('round', $round);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($round->id, pm()->form('round-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    public function update(int $roundId, array $formValues)
    {
        $this->roundService->updateRound($roundId, $formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.round.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }
}
