<?php

namespace App\Ajax\App\Tontine;

use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\ChargeService;
use Siak\Tontine\Validation\Tontine\ChargeValidator;
use App\Ajax\App\Faker;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function Jaxon\pm;
use function config;
use function trans;

class Charge extends CallableClass
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @di
     * @var ChargeService
     */
    protected ChargeService $chargeService;

    /**
     * @var ChargeValidator
     */
    protected ChargeValidator $validator;

    /**
     * @databag charge
     */
    public function home()
    {
        $html = $this->view()->render('tontine.pages.charge.home');
        $this->response->html('section-title', trans('tontine.menus.tontine'));
        $this->response->html('content-home', $html);
        $this->jq('#btn-refresh')->click($this->rq()->home());
        $this->jq('#btn-create')->click($this->rq()->number());

        return $this->page();
    }

    /**
     * @databag charge
     */
    public function page(int $pageNumber = 0)
    {
        $chargeCount = $this->chargeService->getChargeCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $chargeCount, 'charge', 'page');
        $charges = $this->chargeService->getCharges($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $chargeCount);

        $types = ['Frais', 'Amende'];
        $periods = ['Aucune', 'Unique', 'Année', 'Séance'];
        $html = $this->view()->render('tontine.pages.charge.page')
            ->with('charges', $charges)
            ->with('types', $types)
            ->with('periods', $periods)
            ->with('pagination', $pagination);
        $this->response->html('content-page', $html);

        $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
        $this->jq('.btn-charge-edit')->click($this->rq()->edit($chargeId));

        return $this->response;
    }

    public function number()
    {
        $title = trans('number.labels.title');
        $content = $this->view()->render('tontine.pages.charge.number');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.add'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->add(pm()->input('text-number')->toInt()),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    private function getChargeTypes()
    {
        return [
            trans('tontine.charge.types.fee'),
            trans('tontine.charge.types.fine'),
        ];
    }

    private function getChargePeriods()
    {
        return [
            trans('tontine.charge.periods.none') . ' (' . trans('tontine.charge.types.fine') . ')',
            trans('tontine.charge.periods.unique'),
            trans('tontine.charge.periods.round'),
            trans('tontine.charge.periods.session'),
        ];
    }

    /**
     * @di $localeService
     * @databag faker
     */
    public function add(int $count)
    {
        if($count <= 0)
        {
            $this->notify->warning(trans('number.errors.invalid'));
            return $this->response;
        }
        if($count > 10)
        {
            $this->notify->warning(trans('number.errors.max', ['max' => 10]));
            return $this->response;
        }

        $this->dialog->hide();

        $tontine = $this->chargeService->getTontine();
        [, $currency] = $this->localeService->getNameFromTontine($tontine);

        $useFaker = config('jaxon.app.faker', false);
        $html = $this->view()->render('tontine.pages.charge.add')
            ->with('useFaker', $useFaker)
            ->with('count', $count)
            ->with('currency', $currency)
            ->with('types', $this->getChargeTypes())
            ->with('periods', $this->getChargePeriods());
        $this->response->html('content-home', $html);
        $this->jq('#btn-cancel')->click($this->rq()->home());
        $this->jq('#btn-save')->click($this->rq()->create(pm()->form('charge-form')));
        if($useFaker)
        {
            $this->bag('faker')->set('charge.count', $count);
            $this->jq('#btn-fakes')->click($this->cl(Faker::class)->rq()->charges());
        }

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateList($formValues['charges'] ?? []);

        $this->chargeService->createCharges($values);
        $this->notify->success(trans('tontine.charge.messages.created'), trans('common.titles.success'));

        return $this->home();
    }

    /**
     * @di $localeService
     */
    public function edit(int $chargeId)
    {
        $charge = $this->chargeService->getCharge($chargeId);

        $tontine = $this->chargeService->getTontine();
        [, $currency] = $this->localeService->getNameFromTontine($tontine);

        $title = trans('tontine.charge.titles.edit');
        $content = $this->view()
            ->render('tontine.pages.charge.edit')
            ->with('charge', $charge)
            ->with('currency', $currency)
            ->with('types', $this->getChargeTypes())
            ->with('periods', $this->getChargePeriods());
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($charge->id, pm()->form('charge-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     * @databag charge
     */
    public function update(int $chargeId, array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $charge = $this->chargeService->getCharge($chargeId);

        $this->chargeService->updateCharge($charge, $values);
        $this->dialog->hide();
        $this->page(); // Back to current page
        $this->notify->success(trans('tontine.charge.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }

    /*public function delete(int $chargeId)
    {
        $this->notify->error("Cette fonction n'est pas encore disponible", trans('common.titles.error'));

        return $this->response;
    }*/
}
