<?php

namespace Ajax\App\Tontine\Options;

use Ajax\Component;
use Ajax\App\SectionContent;
use Ajax\App\SectionTitle;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\OptionsValidator;

use function Jaxon\pm;
use function trans;

class Options extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @var OptionsValidator
     */
    protected OptionsValidator $validator;

    public function __construct(private TontineService $tontineService)
    {}

    /**
     * @before checkGuestAccess ["tontine", "categories"]
     * @databag charge
     * @after hideMenuOnMobile
     */
    public function home(): AjaxResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontine'));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.options.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->setSmScreenHandler('options-sm-screens');

        $this->cl(FundPage::class)->page();
        $this->cl(CategoryPage::class)->page();
        $this->cl(ChargePage::class)->page();
    }

    public function editOptions()
    {
        $options = $this->tontineService->getTontineOptions();
        $template = $options['reports']['template'] ?? 'default';
        $title = trans('tontine.options.titles.edit');
        $content = $this->renderView('pages.options.edit', [
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
