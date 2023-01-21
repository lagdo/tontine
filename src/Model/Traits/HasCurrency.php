<?php

namespace Siak\Tontine\Model\Traits;

use Siak\Tontine\Service\LocaleService;

use function app;
use function intval;

trait HasCurrency
{
    /**
     * Format an attribute value
     *
     * @param string $attr
     * @param bool $hideSymbol
     *
     * @return string
     */
    public function money(string $attr, bool $hideSymbol = false): string
    {
        return app(LocaleService::class)->formatCurrency(intval($this->$attr), $hideSymbol);
    }
}
