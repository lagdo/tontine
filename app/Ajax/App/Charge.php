<?php

namespace App\Ajax\App;

use Siak\Tontine\Service\ChargeService;
use Siak\Tontine\Validation\ChargeValidator;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;
use function intval;

class Charge extends CallableClass
{
    /**
     * @di
     * @var ChargeService
     */
    public ChargeService $chargeService;

    /**
     * @var ChargeValidator
     */
    public ChargeValidator $validator;

    /**
     * @databag charge
     */
    public function home()
    {
        $html = $this->view()->render('pages.charge.home');
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
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('charge')->get('page', 1);
        }
        $this->bag('charge')->set('page', $pageNumber);

        $charges = $this->chargeService->getCharges($pageNumber);
        $chargeCount = $this->chargeService->getChargeCount();

        $types = ['Frais', 'Amende'];
        $periods = ['Aucune', 'Unique', 'Année', 'Séance'];
        $html = $this->view()->render('pages.charge.page')
            ->with('charges', $charges)->with('types', $types)->with('periods', $periods)
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $chargeCount));
        $this->response->html('content-page', $html);

        $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
        $this->jq('.btn-charge-edit')->click($this->rq()->edit($chargeId));

        return $this->response;
    }

    public function number()
    {
        $title = trans('number.labels.title');
        $content = $this->view()->render('pages.charge.number');
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

    /**
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
        $this->bag('faker')->set('charge.count', $count);

        $types = ['Frais', 'Amende'];
        $periods = ['Aucune', 'Unique', 'Année', 'Séance'];
        $html = $this->view()->render('pages.charge.add')->with('count', $count)
            ->with('types', $types)->with('periods', $periods);
        $this->response->html('content-home', $html);
        $this->jq('#btn-cancel')->click($this->rq()->home());
        $this->jq('#btn-fakes')->click($this->rq()->fakes());
        $this->jq('#btn-save')->click($this->rq()->create(pm()->form('charge-form')));

        return $this->response;
    }

    /**
     * @databag faker
     */
    public function fakes()
    {
        $count = intval($this->bag('faker')->get('charge.count'));
        $charges = $this->chargeService->getFakeCharges($count);
        for($i = 0; $i < $count; $i++)
        {
            $this->jq("#charge_type_$i")->val($charges[$i]->type);
            $this->jq("#charge_period_$i")->val($charges[$i]->period);
            $this->jq("#charge_name_$i")->val($charges[$i]->name);
            $this->jq("#charge_amount_$i")->val($charges[$i]->amount);
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

    public function edit(int $chargeId)
    {
        $charge = $this->chargeService->getCharge($chargeId);

        $title = trans('tontine.charge.titles.edit');
        $types = ['Frais', 'Amende'];
        $periods = ['Aucune', 'Unique', 'Année', 'Séance'];
        $content = $this->view()->render('pages.charge.edit')->with('charge', $charge)
            ->with('types', $types)->with('periods', $periods);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($charge->id, pm()->form('charge-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

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
