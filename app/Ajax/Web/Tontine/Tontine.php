<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\Component;
use App\Ajax\SelectTrait;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\TontineValidator;

use function Jaxon\pm;
use function collect;
use function trans;

/**
 * @databag tontine
 */
class Tontine extends Component
{
    use SelectTrait;

    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var MemberService
     */
    protected MemberService $memberService;

    /**
     * @var TontineValidator
     */
    protected TontineValidator $validator;

    /**
     * @param TontineService $tontineService
     */
    public function __construct(private TontineService $tontineService)
    {}

    /**
     * @after hideMenuOnMobile
     */
    public function home(): ComponentResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontines'));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.tontine.home', [
            'hasGuestTontines' => $this->tontineService->hasGuestTontines(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        if(($tontine = $this->tenantService->tontine()))
        {
            $this->selectTontine($tontine);
        }
        if(($round = $this->tenantService->round()))
        {
            $this->selectRound($round);
        }

        $this->cl(TontinePage::class)->page();
    }

    /**
     * @di $localeService
     */
    public function add()
    {
        $title = trans('tontine.titles.add');
        $content = $this->renderView('pages.tontine.add', [
            'countries' => $this->localeService->getCountries(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('tontine-form')),
        ]];

        $this->dialog->hide();
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->tontineService->createTontine($values);

        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.created'));
        $this->cl(TontinePage::class)->page(); // Back to current page

        return $this->response;
    }

    /**
     * @di $localeService
     */
    public function edit(int $tontineId)
    {
        $tontine = $this->tontineService->getTontine($tontineId);

        $title = trans('tontine.titles.edit');
        [, $currencies] = $this->localeService->getNamesFromTontines(collect([$tontine]));
        $content = $this->renderView('pages.tontine.edit', [
            'tontine' => $tontine,
            'countries' => $this->localeService->getCountries(),
            'currencies' => $currencies
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($tontine->id, pm()->form('tontine-form')),
        ]];

        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function update(int $tontineId, array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->tontineService->updateTontine($tontineId, $values);

        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.updated'));
        $this->cl(TontinePage::class)->page(); // Back to current page

        return $this->response;
    }

    public function delete(int $tontineId)
    {
        $this->tontineService->deleteTontine($tontineId);

        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.deleted'));
        $this->cl(TontinePage::class)->page(); // Back to current page
    }
}
