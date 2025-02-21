<?php

namespace Ajax\App\Planning\Session;

use Ajax\FuncComponent;
use Ajax\App\Tontine\SelectFunc;
use Siak\Tontine\Service\Planning\RoundService;

use function Jaxon\pm;
use function trans;

/**
 * @databag planning
 */
class RoundFunc extends FuncComponent
{
    /**
     * @param RoundService $roundService
     */
    public function __construct(protected RoundService $roundService)
    {}

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
        $this->modal()->show($title, $content, $buttons);
    }

    public function create(array $formValues)
    {
        $this->roundService->createRound($formValues);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.created'));
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
        $this->modal()->show($title, $content, $buttons);
    }

    public function update(int $roundId, array $formValues)
    {
        $this->roundService->updateRound($roundId, $formValues);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.updated'));
    }

    public function delete(int $roundId)
    {
        $this->roundService->deleteRound($roundId);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.deleted'));

        $currentRound = $this->tenantService->round();
        if($currentRound !== null && $currentRound->id === $roundId)
        {
            // If the currently selected round is deleted, then choose another.
            $this->cl(SelectFunc::class)
                ->saveOrganisation($this->tenantService->tontine()->id);
        }
    }
}
