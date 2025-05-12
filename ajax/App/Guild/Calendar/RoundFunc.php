<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\FuncComponent;
use Ajax\Page\MainTitle;
use Siak\Tontine\Service\Guild\RoundService;

use function Jaxon\pm;
use function trans;

/**
 * @databag guild.calendar
 * @before checkHostAccess ["guild", "calendar"]
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
        $content = $this->renderView('pages.guild.calendar.round.add');
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

        $this->cl(MainTitle::class)->render();
    }

    public function edit(int $roundId)
    {
        $round = $this->roundService->getRound($roundId);

        $title = trans('tontine.round.titles.edit');
        $content = $this->renderView('pages.guild.calendar.round.edit')->with('round', $round);
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

        $this->cl(MainTitle::class)->render();
    }
}
