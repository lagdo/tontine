<?php

namespace App\Ajax\Web\Tontine\Options;

use Siak\Tontine\Model\Charge as ChargeModel;

use function trans;

trait ChargeTrait
{
    /**
     * @const
     */
    private static $GROUP_FIXED = 0;

    /**
     * @const
     */
    private static $GROUP_VARIABLE = 1;

    private function getChargeGroups()
    {
        return [
            self::$GROUP_FIXED => trans('tontine.charge.groups.fixed'),
            self::$GROUP_VARIABLE => trans('tontine.charge.groups.variable'),
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
        return $group === self::$GROUP_FIXED ? [
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
        return $group === self::$GROUP_FIXED ? [
            // Values for fixed charges
            ChargeModel::PERIOD_ONCE => trans('tontine.charge.periods.unique'),
            ChargeModel::PERIOD_ROUND => trans('tontine.charge.periods.round'),
            ChargeModel::PERIOD_SESSION => trans('tontine.charge.periods.session'),
        ] : [
            // Values for variable charges
            ChargeModel::PERIOD_NONE => trans('tontine.charge.periods.none'),
        ];
    }
}
