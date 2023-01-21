<?php

namespace Siak\Tontine\Model\Traits;

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
        return intval($this->$attr); // Currency::format(intval($this->$attr), $hideSymbol);
    }
}
