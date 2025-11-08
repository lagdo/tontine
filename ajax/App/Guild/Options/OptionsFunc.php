<?php

namespace Ajax\App\Guild\Options;

use Ajax\FuncComponent;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Validation\Guild\OptionsValidator;

use function je;
use function trans;

class OptionsFunc extends FuncComponent
{
    /**
     * @var OptionsValidator
     */
    protected OptionsValidator $validator;

    public function __construct(private GuildService $guildService)
    {}

    public function editOptions(): void
    {
        $guild = $this->stash()->get('tenant.guild');
        $options = $this->guildService->getGuildOptions($guild);
        $template = $options['reports']['template'] ?? 'raptor';
        $title = trans('tontine.options.titles.edit');
        $content = $this->renderView('pages.guild.options.edit', [
            'template' => $template,
            'templates' => [
                'raptor' => 'Raptor',
                'legacy' => trans('tontine.options.labels.legacy'),
            ],
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveOptions(je('options-form')->rd()->form()),
        ]];

        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function saveOptions(array $formValues): void
    {
        // Validation
        $guild = $this->stash()->get('tenant.guild');
        $options = $this->validator->validateItem($formValues);
        $this->guildService->saveGuildOptions($guild, $options);

        $this->modal()->hide();
        $this->alert()->success(trans('tontine.options.messages.saved'));
    }
}
