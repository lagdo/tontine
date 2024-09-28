<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\CallableClass;
use App\Ajax\Web\Tontine\Select;
use Siak\Tontine\Service\Planning\RoundService;

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
     * @before checkGuestAccess ["planning", "sessions"]
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $html = $this->renderView('pages.planning.round.home');
        $this->response->html('content-home', $html);

        $this->cl(RoundPage::class)->page();
        return $this->cl(Session::class)->show($this->tenantService->round());
    }

    public function add()
    {
        $title = trans('tontine.round.titles.add');
        $content = $this->renderView('pages.planning.round.add');
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

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.created'));

        return $this->response;
    }

    public function edit(int $roundId)
    {
        $round = $this->roundService->getRound($roundId);

        $title = trans('tontine.round.titles.edit');
        $content = $this->renderView('pages.planning.round.edit')->with('round', $round);
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

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.updated'));

        return $this->response;
    }

    public function delete(int $roundId)
    {
        $this->roundService->deleteRound($roundId);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.deleted'));

        $currentRound = $this->tenantService->round();
        if($currentRound !== null && $currentRound->id === $roundId)
        {
            // If the currently selected round is deleted, then choose another.
            $this->cl(Select::class)->saveTontine($this->tenantService->tontine()->id);
        }

        return $this->response;
    }
}
