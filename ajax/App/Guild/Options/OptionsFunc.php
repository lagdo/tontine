<?php

namespace Ajax\App\Guild\Options;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Validation\Guild\OptionsValidator;

use function Jaxon\pm;
use function trans;

class OptionsFunc extends FuncComponent
{
    /**
     * @var OptionsValidator
     */
    protected OptionsValidator $validator;

    public function __construct(private GuildService $guildService)
    {}

    public function editOptions()
    {
        $options = $this->guildService->getGuildOptions();
        $template = $options['reports']['template'] ?? 'default';
        $title = trans('tontine.options.titles.edit');
        $content = $this->renderView('pages.guild.options.edit', [
            'template' => $template,
            'templates' => [
                'default' => trans('tontine.options.labels.default'),
                'raptor' => 'Raptor',
            ],
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveOptions(pm()->form('options-form')),
        ]];

        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function saveOptions(array $formValues)
    {
        // Validation
        $options = $this->validator->validateItem($formValues);
        $this->guildService->saveGuildOptions($options);

        $this->modal()->hide();
        $this->alert()->success(trans('tontine.options.messages.saved'));
    }
}
