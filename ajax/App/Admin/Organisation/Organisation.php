<?php

namespace Ajax\App\Admin\Organisation;

use Ajax\Component;
use Ajax\SelectTrait;
use Ajax\App\Admin\User\Guest\Organisation as GuestOrganisation;
use Ajax\App\SectionContent;
use Ajax\App\SectionTitle;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\TontineValidator;
use Stringable;

use function Jaxon\pm;
use function collect;
use function trans;

/**
 * @databag tontine
 */
class Organisation extends Component
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
    public function home(): AjaxResponse
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
    public function html(): Stringable
    {
        return $this->renderView('pages.tontine.home', [
            'hasGuestOrganisations' => $this->tontineService->hasGuestOrganisations(),
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

        $this->cl(OrganisationPage::class)->page();

        if($this->tontineService->hasGuestOrganisations())
        {
            $this->cl(GuestOrganisation::class)->render();
        }
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

        $this->modal()->hide();
        $this->modal()->show($title, $content, $buttons);

        return $this->response;
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

        $this->modal()->show($title, $content, $buttons);

        return $this->response;
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

        return $this->response;
    }

    public function delete(int $tontineId)
    {
        $this->tontineService->deleteTontine($tontineId);

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.deleted'));
        $this->cl(OrganisationPage::class)->page(); // Back to current page

        return $this->response;
    }
}
