<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\FuncComponent;
use Ajax\Page\Header\GuildHeader;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Guild\RoundService;
use Siak\Tontine\Validation\Guild\RoundValidator;

use function je;
use function trans;

#[Before('checkHostAccess', ["guild", "calendar"])]
#[Databag('guild.calendar')]
class RoundFunc extends FuncComponent
{
    /**
     * @var RoundValidator
     */
    protected RoundValidator $validator;

    /**
     * @param RoundService $roundService
     */
    public function __construct(protected RoundService $roundService)
    {}

    public function add(): void
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

    #[Inject(attr: 'validator')]
    public function create(array $formValues): void
    {
        $guild = $this->stash()->get('tenant.guild');
        $values = $this->validator->validateItem($formValues);
        $options = [
            'members' => isset($formValues['members']),
            'charges' => isset($formValues['charges']),
            'savings' => isset($formValues['savings']),
        ];
        $this->roundService->createRound($guild, $values, $options);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.created'));

        $this->cl(GuildHeader::class)->render();
    }

    public function edit(int $roundId): void
    {
        $guild = $this->stash()->get('tenant.guild');
        $round = $this->roundService->getRound($guild, $roundId);

        $title = trans('tontine.round.titles.edit');
        $content = $this->renderView('pages.guild.calendar.round.edit', [
            'round' => $round,
        ]);
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

    #[Inject(attr: 'validator')]
    public function update(int $roundId, array $formValues): void
    {
        $guild = $this->stash()->get('tenant.guild');
        $values = $this->validator->validateItem($formValues);
        $this->roundService->updateRound($guild, $roundId, $values);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.updated'));
    }

    public function delete(int $roundId): void
    {
        $guild = $this->stash()->get('tenant.guild');
        $this->roundService->deleteRound($guild, $roundId);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.deleted'));

        $this->cl(GuildHeader::class)->render();
    }
}
