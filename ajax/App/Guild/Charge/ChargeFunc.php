<?php

namespace Ajax\App\Guild\Charge;

use Ajax\FuncComponent;
use Siak\Tontine\Model\Charge as ChargeModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Guild\ChargeService;
use Siak\Tontine\Validation\Guild\ChargeValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag charge
 * @before checkHostAccess ["finance", "charges"]
 */
class ChargeFunc extends FuncComponent
{
    use ChargeTrait;

    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var ChargeValidator
     */
    protected ChargeValidator $validator;

    /**
     * @param ChargeService $chargeService
     */
    public function __construct(protected ChargeService $chargeService)
    {}

    public function select()
    {
        $title = '';
        $content = $this->renderView('pages.guild.charge.select', [
            'groups' => $this->getChargeGroups()
        ]);
        $group = pm()->input('charge-group')->toInt();
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.add'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->add($group),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $localeService
     * @databag faker
     */
    public function add(int $group)
    {
        $this->modal()->hide();

        $guild = $this->tenantService->guild();
        [, $currency] = $this->localeService->getNameFromGuild($guild);

        $title = trans('tontine.charge.titles.add');
        $content = $this->renderView('pages.guild.charge.add', [
            'fixed' => $group === self::$GROUP_FIXED,
            'label' => $this->getChargeGroups()[$group],
            'currency' => $currency,
            'types' => $this->getChargeTypes($group),
            'periods' => $this->getChargePeriods($group),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create($group, pm()->form('charge-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function create(int $group, array $formValues)
    {
        if($group !== self::$GROUP_FIXED && $group !== self::$GROUP_VARIABLE)
        {
            $this->response;
        }
        // Set the missing field in each item of the charge array.
        if($group === self::$GROUP_FIXED) // Fixed charge
        {
            $formValues['type'] = ChargeModel::TYPE_FEE;
        }
        if($group === self::$GROUP_VARIABLE) // Variable charge
        {
            $formValues['period'] = ChargeModel::PERIOD_NONE;
            if(empty($formValues['fixed']))
            {
                $formValues['amount'] = 0;
            }
        }
        $values = $this->validator->validateItem($formValues);

        $this->chargeService->createCharge($values);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.charge.messages.created'));
        $this->modal()->hide();

        $this->cl(ChargePage::class)->page();
    }

    /**
     * @di $localeService
     */
    public function edit(int $chargeId)
    {
        $charge = $this->chargeService->getCharge($chargeId);

        $guild = $this->tenantService->guild();
        [, $currency] = $this->localeService->getNameFromGuild($guild);

        $title = trans('tontine.charge.titles.edit');
        $content = $this->renderView('pages.guild.charge.edit', [
            'charge' => $charge,
            'currency' => $currency,
            'types' => $this->getChargeTypes(),
            'periods' => $this->getChargePeriods(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($charge->id, pm()->form('charge-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
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

        $this->chargeService->updateCharge($charge, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.charge.messages.updated'));

        $this->cl(ChargePage::class)->page();
    }

    public function toggle(int $chargeId)
    {
        $charge = $this->chargeService->getCharge($chargeId);
        $this->chargeService->toggleCharge($charge);

        $this->cl(ChargePage::class)->page();
    }

    public function delete(int $chargeId)
    {
        $charge = $this->chargeService->getCharge($chargeId);
        $this->chargeService->deleteCharge($charge);

        $this->cl(ChargePage::class)->page();
    }
}
