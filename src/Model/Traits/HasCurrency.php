<?php

namespace Siak\Tontine\Model\Traits;

use Siak\Tontine\Model\Currency;

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
        return Currency::format(intval($this->$attr), $hideSymbol);
    }
}
