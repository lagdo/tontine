<?php

namespace Ajax\Page\Header;

use Ajax\Base;
use Ajax\Page\Sidebar\GuildMenu;
use Ajax\Page\Sidebar\RoundMenu;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Exclude;
use Siak\Tontine\Model\Round as RoundModel;
use Siak\Tontine\Service\Guild\RoundService;

use function Jaxon\select;
use function trans;
use function view;

#[Before('getCurrentGuild')]
class RoundMenuFunc extends Base\FuncComponent
{
    use Base\Guild\ComponentTrait;
    use Base\Round\ComponentTrait;

    /**
     * @param RoundService $roundService
     */
    public function __construct(protected RoundService $roundService)
    {}

    public function showRounds(): void
    {
        $title = trans('tontine.round.titles.choose');
        $content = $this->renderTpl('parts.header.select.round', [
            'current' => $this->tenantService->getLatestRoundId(),
            'rounds' => $this->tenantService->getRounds(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('tontine.actions.choose'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->selectRound(select('round_id')->toInt()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @param RoundModel $round
     *
     * @return void
     */
    #[Exclude]
    public function setCurrentRound(RoundModel $round): void
    {
        $this->tenantService->setRound($round);

        $this->bag('tenant')->set('round.id', $round?->id ?? 0);
        $this->stash()->set('tenant.round', $round);

        view()->share('currentRound', $round);

        $this->cl(GuildHeader::class)->render();
        $this->cl(RoundMenu::class)->render();
        $this->cl(SectionHeader::class)->currency();
    }

    public function selectRound(int $roundId): void
    {
        if(!($round = $this->tenantService->getRound($roundId)) ||
            $this->roundService->getSessionCount($round) === 0)
        {
            // Todo: show an error message.
            return;
        }

        // Throws an exception.
        $this->checkHostAccess('planning', 'enrollment');

        $this->setCurrentRound($round);

        $this->modal()->hide();
        $this->alert()->info(trans('tontine.round.messages.selected', [
            'guild' => $round->guild->name,
            'round' => $round->title,
        ]));
    }

    /**
     * @return void
     */
    private function resetCurrentRound(): void
    {
        $this->bag('tenant')->set('round.id', 0);
        $this->stash()->set('tenant.round', null);

        view()->share('currentRound', null);
    }

    /**
     * Go back to the Guild section.
     *
     * @return void
     */
    #[Before('getCurrentRound')]
    public function back(): void
    {
        // Take the current round before resetting the values.
        $round = $this->stash()->get('tenant.round');
        $this->resetCurrentRound();

        $this->cl(GuildHeader::class)->render();
        $this->cl(GuildMenu::class)->render();
        $this->cl(SectionHeader::class)->currency();

        if($round !== null)
        {
            $this->alert()->info(trans('tontine.messages.back_to_guild', [
                'round' => $round->title,
            ]));
        }
    }
}
