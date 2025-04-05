<?php

namespace Ajax\App\Admin\Organisation;

use Ajax\App\Page\MainTitle;
use Ajax\App\Page\Sidebar\AdminMenu;
use Ajax\FuncComponent;
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
class OrganisationFunc extends FuncComponent
{
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
     * @di $localeService
     */
    public function add()
    {
        $title = trans('tontine.titles.add');
        $content = $this->renderView('pages.admin.organisation.add', [
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

        $this->modal()->hide();
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->tontineService->createTontine($values);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.created'));
        $this->cl(OrganisationPage::class)->page(); // Back to current page

        $this->cl(MainTitle::class)->render();
        $this->cl(AdminMenu::class)->render();
    }

    /**
     * @di $localeService
     */
    public function edit(int $tontineId)
    {
        $tontine = $this->tontineService->getTontine($tontineId);

        $title = trans('tontine.titles.edit');
        [, $currencies] = $this->localeService->getNamesFromTontines(collect([$tontine]));
        $content = $this->renderView('pages.admin.organisation.edit', [
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

        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function update(int $tontineId, array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->tontineService->updateTontine($tontineId, $values);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.updated'));
        $this->cl(OrganisationPage::class)->page(); // Back to current page
    }

    public function delete(int $tontineId)
    {
        $this->tontineService->deleteTontine($tontineId);

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.deleted'));
        $this->cl(OrganisationPage::class)->page(); // Back to current page

        $this->cl(MainTitle::class)->render();
        $this->cl(AdminMenu::class)->render();
    }
}
