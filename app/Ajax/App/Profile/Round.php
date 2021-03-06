<?php

namespace App\Ajax\App\Profile;

use Siak\Tontine\Service\RoundService;
use Siak\Tontine\Service\TenantService;
use App\Ajax\CallableClass;

use function intval;
use function jq;
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
        $html = $this->view()->render('pages.profile.round.home')->with('tontine', $this->tontine);
        $this->response->html('round-home', $html);

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

        $html = $this->view()->render('pages.profile.round.page')
            ->with('rounds', $rounds)
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $roundCount));
        $this->response->html('round-page', $html);

        $roundId = jq()->parent()->attr('data-round-id')->toInt();
        $this->jq('.btn-round-edit')->click($this->rq()->edit($roundId));
        $this->jq('.btn-round-select')->click($this->rq()->select($roundId));

        return $this->response;
    }

    public function select(int $roundId)
    {
        if(!($round = $this->roundService->getRound(intval($roundId))))
        {
            return $this->response;
        }

        // Save the tontine and round ids in the user session.
        session(['tontine.id' => $this->tontine->id, 'round.id' => $round->id]);

        // Reload the page
        $this->response->redirect('/');

        return $this->response;
    }

    public function add()
    {
        $title = trans('tontine.round.labels.add');
        $content = $this->view()->render('pages.profile.round.add');
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

        $title = trans('tontine.round.labels.edit');
        $content = $this->view()->render('pages.profile.round.edit')->with('round', $round);
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
