<?php

namespace App\Ajax\App\Tontine;

use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\TontineValidator;
use App\Ajax\App\Locale;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function Jaxon\pm;
use function collect;
use function trans;

class Tontine extends CallableClass
{
    /**
     * @di
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @di
     * @var TontineService
     */
    protected TontineService $tontineService;

    /**
     * @var RoundService
     */
    protected RoundService $roundService;

    /**
     * @var MemberService
     */
    protected MemberService $memberService;

    /**
     * @var TontineValidator
     */
    protected TontineValidator $validator;

    /**
     * @di $tenantService
     * @di $roundService
     * @databag tontine
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.tontines'));
        $this->response->html('content-home', $this->view()->render('tontine.pages.tontine.home'));

        if(($tontine = $this->tenantService->tontine()))
        {
            $this->selectTontine($tontine);
        }
        if(($round = $this->tenantService->round()))
        {
            $this->selectRound($round);
        }

        $this->jq('#btn-tontine-create')->click($this->rq()->selectType());
        $this->jq('#btn-tontine-refresh')->click($this->rq()->home());
        $this->jq('#btn-show-select')->click($this->cl(Select::class)->rq()->showTontines());

        return $this->page();
    }

    /**
     * @databag tontine
     */
    public function page(int $pageNumber = 0)
    {
        $tontineCount = $this->tontineService->getTontineCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $tontineCount, 'tontine', 'page');
        $tontines = $this->tontineService->getTontines($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $tontineCount);
        [$countries, $currencies] = $this->localeService->getNamesFromTontines($tontines);

        $html = $this->view()->render('tontine.pages.tontine.page')
            ->with('types', $this->tontineService->getTontineTypes())
            ->with('tontines', $tontines)
            ->with('countries', $countries)
            ->with('currencies', $currencies)
            ->with('pagination', $pagination);
        $this->response->html('tontine-page', $html);

        $this->setTontineButtonHandlers();

        return $this->response;
    }

    public function selectType()
    {
        $title = trans('tontine.titles.type');
        $content = $this->view()->render('tontine.pages.tontine.type')
            ->with('types', $this->tontineService->getTontineTypes())
            ->with('descriptions', $this->tontineService->getTontineDescriptions());
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.add'),
            'class' => 'btn btn-primary btn-select-type',
            'click' => '',
        ]];
        $this->dialog->show($title, $content, $buttons);
        $this->jq('.btn-select-type')->on('click', $this->rq()->add(jq('input[name="type"]:checked')->val()));

        return $this->response;
    }

    /**
     * @di $localeService
     */
    public function add(string $type)
    {
        $title = trans('tontine.titles.add');
        $content = $this->view()->render('tontine.pages.tontine.add')
            ->with('type', $type)
            ->with('countries', $this->localeService->getCountries());
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
        $this->jq('#select_country_dropdown')
            ->change($this->cl(Locale::class)->rq()->selectCurrencies(jq()->val()));

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $formValues = $this->validator->validateItem($formValues);

        $this->tontineService->createTontine($formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.messages.created'), trans('common.titles.success'));

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
        $content = $this->view()->render('tontine.pages.tontine.edit')
            ->with('tontine', $tontine)
            ->with('types', $this->tontineService->getTontineTypes())
            ->with('countries', $this->localeService->getCountries())
            ->with('currencies', $currencies);
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
        $this->jq('#select_country_dropdown')
            ->change($this->cl(Locale::class)->rq()->selectCurrencies(jq()->val()));

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function update(int $tontineId, array $formValues)
    {
        $formValues = $this->validator->validateItem($formValues);

        $this->tontineService->updateTontine($tontineId, $formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }
}
