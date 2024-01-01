<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\OptionsValidator;

use function Jaxon\pm;
use function trans;

class Options extends CallableClass
{
    /**
     * @var OptionsValidator
     */
    protected OptionsValidator $validator;

    public function __construct(private TontineService $tontineService)
    {}

    /**
     * @databag charge
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.tontine'));
        $this->response->html('content-home', $this->render('pages.tontine.options'));

        $this->cl(Fund::class)->show();
        $this->cl(Category::class)->show();
        $this->cl(Charge::class)->show();
    }

    public function editOptions()
    {
        $options = $this->tontineService->getTontineOptions();
        $template = $options['reports']['template'] ?? 'default';
        $title = trans('tontine.options.titles.edit');
        $content = $this->render('pages.tontine.options.edit', [
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

        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveOptions(array $formValues)
    {
        // Validation
        $options = $this->validator->validateItem($formValues);
        $this->tontineService->saveTontineOptions($options);

        $this->dialog->hide();
        $this->notify->success(trans('tontine.options.messages.saved'));

        return $this->response;
    }
}
