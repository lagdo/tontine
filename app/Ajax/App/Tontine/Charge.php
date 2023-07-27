<?php

namespace App\Ajax\App\Tontine;

use Siak\Tontine\Model\Charge as ChargeModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\ChargeService;
use Siak\Tontine\Validation\Tontine\ChargeValidator;
use App\Ajax\App\Faker;
use App\Ajax\CallableClass;

use function array_map;
use function Jaxon\jq;
use function Jaxon\pm;
use function config;
use function trans;

class Charge extends CallableClass
{
    /**
     * @const
     */
    const GROUP_FIXED = 0;

    /**
     * @const
     */
    const GROUP_VARIABLE = 1;

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
     * @after hideMenuOnMobile
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

    private function getChargeGroups()
    {
        return [
            self::GROUP_FIXED => trans('tontine.charge.groups.fixed'),
            self::GROUP_VARIABLE => trans('tontine.charge.groups.variable'),
        ];
    }

    private function getChargeTypes(int $group = -1)
    {
        if($group < 0)
        {
            // All possible values
            return [
                ChargeModel::TYPE_FEE => trans('tontine.charge.types.fee'),
                ChargeModel::TYPE_FINE => trans('tontine.charge.types.fine'),
            ];
        }
        return $group === self::GROUP_FIXED ? [
            // Values for fixed charges
            ChargeModel::TYPE_FEE => trans('tontine.charge.types.fee'),
        ] : [
            // Values for variable charges
            ChargeModel::TYPE_FEE => trans('tontine.charge.types.fee'),
            ChargeModel::TYPE_FINE => trans('tontine.charge.types.fine'),
        ];
    }

    private function getChargePeriods(int $group = -1)
    {
        if($group < 0)
        {
            // All possible values
            return [
                ChargeModel::PERIOD_NONE => trans('tontine.charge.periods.none'),
                ChargeModel::PERIOD_ONCE => trans('tontine.charge.periods.unique'),
                ChargeModel::PERIOD_ROUND => trans('tontine.charge.periods.round'),
                ChargeModel::PERIOD_SESSION => trans('tontine.charge.periods.session'),
            ];
        }
        return $group === self::GROUP_FIXED ? [
            // Values for fixed charges
            ChargeModel::PERIOD_ONCE => trans('tontine.charge.periods.unique'),
            ChargeModel::PERIOD_ROUND => trans('tontine.charge.periods.round'),
            ChargeModel::PERIOD_SESSION => trans('tontine.charge.periods.session'),
        ] : [
            // Values for variable charges
            ChargeModel::PERIOD_NONE => trans('tontine.charge.periods.none'),
        ];
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

        $types = $this->getChargeTypes();
        $periods = $this->getChargePeriods();
        $html = $this->view()->render('tontine.pages.charge.page')
            ->with('charges', $charges)
            ->with('types', $types)
            ->with('periods', $periods)
            ->with('pagination', $pagination);
        $this->response->html('content-page', $html);

        $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
        $this->jq('.btn-charge-edit')->click($this->rq()->edit($chargeId));
        $this->jq('.btn-charge-toggle')->click($this->rq()->toggle($chargeId));

        return $this->response;
    }

    public function number()
    {
        $title = trans('number.labels.title');
        $content = $this->view()->render('tontine.pages.charge.number')
            ->with('groups', $this->getChargeGroups());
        $group = pm()->input('charge-group')->toInt();
        $number = pm()->input('text-number')->toInt();
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.add'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->add($group, $number),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $localeService
     * @databag faker
     */
    public function add(int $group, int $count)
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
            ->with('fixed', $group === self::GROUP_FIXED)
            ->with('label', $this->getChargeGroups()[$group])
            ->with('count', $count)
            ->with('currency', $currency)
            ->with('types', $this->getChargeTypes($group))
            ->with('periods', $this->getChargePeriods($group));
        $this->response->html('content-home', $html);
        $this->jq('#btn-cancel')->click($this->rq()->home());
        $this->jq('#btn-save')->click($this->rq()->create($group, pm()->form('charge-form')));
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
    public function create(int $group, array $formValues)
    {
        if($group !== self::GROUP_FIXED && $group !== self::GROUP_VARIABLE)
        {
            $this->response;
        }
        // Set the missing field in each item of the charge array.
        $charges = array_map(function($charge) use($group) {
            if($group === self::GROUP_FIXED) // Fixed charge
            {
                $charge['type'] = ChargeModel::TYPE_FEE;
            }
            if($group === self::GROUP_VARIABLE) // Variable charge
            {
                $charge['period'] = ChargeModel::PERIOD_NONE;
                if(empty($charge['fixed']))
                {
                    $charge['amount'] = 0;
                }
            }
            return $charge;
        }, $formValues['charges'] ?? []);

        $values = $this->validator->validateList($charges);

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
        $charge = $this->chargeService->getCharge($chargeId);
        // These fields cannot be changed
        $formValues['type'] = $charge->type;
        $formValues['period'] = $charge->period;
        if(empty($formValues['fixed']))
        {
            $formValues['amount'] = 0;
        }

        $values = $this->validator->validateItem($formValues);
        $this->logger()->debug('Update charge', compact('values'));

        $this->chargeService->updateCharge($charge, $values);
        $this->dialog->hide();
        $this->page(); // Back to current page
        $this->notify->success(trans('tontine.charge.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }

    /**
     * @databag charge
     */
    public function toggle(int $chargeId)
    {
        $charge = $this->chargeService->getCharge($chargeId);
        $this->chargeService->toggleCharge($charge);

        return $this->page();
    }

    /*public function delete(int $chargeId)
    {
        $this->notify->error("Cette fonction n'est pas encore disponible", trans('common.titles.error'));

        return $this->response;
    }*/
}
