<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;
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
    public function home(): ComponentResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontine'));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.options.home');
    }

    /**
     * @inheritDoc
     */
    public function after()
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
