<?php

namespace Siak\Tontine\Model\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Siak\Tontine\Service\LocaleService;

use function app;
use function intval;

trait HasCurrency
{
    /**
     * Get the amount to display
     *
     * @return Attribute
     */
    public function amountValue(): Attribute
    {
        return Attribute::make(
            get: fn() => app(LocaleService::class)->getMoneyValue(intval($this->amount)),
        );
    }
}
