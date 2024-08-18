<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Siak\Tontine\Service\Meeting\Credit\DebtCalculator;

use function app;

class Refund extends Base
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    /**
     * @return Attribute
     */
    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn() => app()->make(DebtCalculator::class)
                ->getDebtDueAmount($this->debt, $this->session, false),
        );
    }
}
