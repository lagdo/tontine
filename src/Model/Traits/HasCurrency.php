<?php

namespace Siak\Tontine\Model\Traits;

use Siak\Tontine\Service\LocaleService;

use function app;
use function intval;

trait HasCurrency
{
    /**
     * Get the amount to display
     *
     * @return float
     */
    public function getAmountValueAttribute(): float
    {
        return app(LocaleService::class)->getMoneyValue(intval($this->amount));
    }
}
