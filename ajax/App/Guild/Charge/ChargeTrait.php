<?php

namespace Ajax\App\Guild\Charge;

use Siak\Tontine\Model\ChargeDef as ChargeDefModel;

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

    private function getChargeTypes(int $group = -1): array
    {
        if($group < 0)
        {
            // All possible values
            return [
                ChargeDefModel::TYPE_FEE => trans('tontine.charge.types.fee'),
                ChargeDefModel::TYPE_FINE => trans('tontine.charge.types.fine'),
            ];
        }
        return $group === self::$GROUP_FIXED ? [
            // Values for fixed charges
            ChargeDefModel::TYPE_FEE => trans('tontine.charge.types.fee'),
        ] : [
            // Values for variable charges
            ChargeDefModel::TYPE_FEE => trans('tontine.charge.types.fee'),
            ChargeDefModel::TYPE_FINE => trans('tontine.charge.types.fine'),
        ];
    }

    private function getChargePeriods(int $group = -1): array
    {
        if($group < 0)
        {
            // All possible values
            return [
                ChargeDefModel::PERIOD_NONE => trans('tontine.charge.periods.none'),
                ChargeDefModel::PERIOD_ONCE => trans('tontine.charge.periods.unique'),
                ChargeDefModel::PERIOD_ROUND => trans('tontine.charge.periods.round'),
                ChargeDefModel::PERIOD_SESSION => trans('tontine.charge.periods.session'),
            ];
        }
        return $group === self::$GROUP_FIXED ? [
            // Values for fixed charges
            ChargeDefModel::PERIOD_ONCE => trans('tontine.charge.periods.unique'),
            ChargeDefModel::PERIOD_ROUND => trans('tontine.charge.periods.round'),
            ChargeDefModel::PERIOD_SESSION => trans('tontine.charge.periods.session'),
        ] : [
            // Values for variable charges
            ChargeDefModel::PERIOD_NONE => trans('tontine.charge.periods.none'),
        ];
    }
}
