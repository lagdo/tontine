<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\CallableClass;
use App\Ajax\Web\Tontine\Select;
use Siak\Tontine\Service\Planning\RoundService;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag tontine
 */
class Round extends CallableClass
{
    /**
     * @param RoundService $roundService
     */
    public function __construct(protected RoundService $roundService)
    {}

    /**
     * @databag planning
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $html = $this->render('pages.planning.round.home');
        $this->response->html('content-home', $html);

        $this->jq('#btn-show-select')->click($this->cl(Select::class)->rq()->showRounds());
        $this->jq('#btn-round-refresh')->click($this->rq()->home());
        $this->jq('#btn-round-create')->click($this->rq()->add());

        $this->page();

        return $this->cl(Session::class)->setTenantService($this->tenantService)->show();
    }

    public function page(int $pageNumber = 0)
    {
        $roundCount = $this->roundService->getRoundCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $roundCount, 'tontine', 'round.page');
        $rounds = $this->roundService->getRounds($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $roundCount);

        $html = $this->render('pages.planning.round.page')
            ->with('rounds', $rounds)
            ->with('pagination', $pagination);
        $this->response->html('content-page-rounds', $html);

        $roundId = jq()->parent()->attr('data-round-id')->toInt();
        $this->jq('.btn-round-edit')->click($this->rq()->edit($roundId));
        $this->jq('.btn-round-select')->click($this->cl(Select::class)->rq()->saveRound($roundId));
        $this->jq('.btn-round-delete')->click($this->rq()->delete($roundId)
            ->confirm(trans('tontine.round.questions.delete')));

        return $this->response;
    }

    public function add()
    {
        $title = trans('tontine.round.titles.add');
        $content = $this->render('pages.planning.round.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('round-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

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
        $content = $this->render('pages.planning.round.edit')->with('round', $round);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($round->id, pm()->form('round-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

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

    public function delete(int $roundId)
    {
        $this->roundService->deleteRound($roundId);

        $this->page(); // Back to current page
        $this->notify->success(trans('tontine.round.messages.deleted'), trans('common.titles.success'));

        $currentRound = $this->tenantService->round();
        if($currentRound !== null && $currentRound->id === $roundId)
        {
            // If the currently selected round is deleted, then choose another.
            $this->cl(Select::class)->setTenantService($this->tenantService)
                ->saveTontine($this->tenantService->tontine()->id);
        }

        return $this->response;
    }
}
