<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\FuncComponent;
use Ajax\Page\MainTitle;
use Siak\Tontine\Service\Guild\RoundService;

use function je;
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
            'click' => $this->rq()->create(je('round-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function create(array $formValues)
    {
        $guild = $this->stash()->get('tenant.guild');
        $this->roundService->createRound($guild, $formValues);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.created'));

        $this->cl(MainTitle::class)->render();
    }

    public function edit(int $roundId)
    {
        $guild = $this->stash()->get('tenant.guild');
        $round = $this->roundService->getRound($guild, $roundId);

        $title = trans('tontine.round.titles.edit');
        $content = $this->renderView('pages.guild.calendar.round.edit')->with('round', $round);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($round->id, je('round-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function update(int $roundId, array $formValues)
    {
        $guild = $this->stash()->get('tenant.guild');
        $this->roundService->updateRound($guild, $roundId, $formValues);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.updated'));
    }

    public function delete(int $roundId)
    {
        $guild = $this->stash()->get('tenant.guild');
        $this->roundService->deleteRound($guild, $roundId);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.deleted'));

        $this->cl(MainTitle::class)->render();
    }
}
