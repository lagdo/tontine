<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\SelectCallable;
use App\Ajax\Web\Locale;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Tontine\TontineValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function collect;
use function trans;

/**
 * @databag tontine
 */
class Tontine extends SelectCallable
{
    /**
     * @di
     * @var LocaleService
     */
    protected LocaleService $localeService;

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
     * @di $roundService
     * @databag tontine
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.tontines'));
        $this->response->html('content-home', $this->renderView('pages.tontine.home'));
        if(($tontine = $this->tenantService->tontine()))
        {
            $this->selectTontine($tontine);
        }
        if(($round = $this->tenantService->round()))
        {
            $this->selectRound($round);
        }

        return $this->cl(TontinePage::class)->page();
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
        $this->response->jq('#select_country_dropdown')
            ->on('change', $this->rq(Locale::class)->selectCurrency(jq()->val()));

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->tontineService->createTontine($values);
        $this->cl(TontinePage::class)->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.created'));

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
        $this->response->jq('#select_country_dropdown')
            ->on('change', $this->rq(Locale::class)->selectCurrency(jq()->val()));

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function update(int $tontineId, array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->tontineService->updateTontine($tontineId, $values);
        $this->cl(TontinePage::class)->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.updated'));

        return $this->response;
    }

    public function delete(int $tontineId)
    {
        $this->tontineService->deleteTontine($tontineId);
        $this->cl(TontinePage::class)->page(); // Back to current page
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.deleted'));
    }
}
