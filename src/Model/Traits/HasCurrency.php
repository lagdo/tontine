<?php

namespace Siak\Tontine\Model\Traits;

use Siak\Tontine\Model\Currency;

use function number_format;
use function intval;

trait HasCurrency
{
    /**
     * @var Currency
     */
    public static Currency $currency;

    /**
     * Format an amount of money
     *
     * @param int $amount
     * @param bool $hideSymbol
     *
     * @return string
     */
    public static function format(int $amount, bool $hideSymbol = false): string
    {
        $options = self::$currency->options;
        $amount = number_format($amount, $options->precision,
            $options->separator->decimal, $options->separator->thousand);
        if($hideSymbol)
        {
            return $amount;
        }
        return $options->symbol->swap ? $options->symbol->value . $amount : $amount . ' ' . $options->symbol->value;
    }

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
        return self::format(intval($this->$attr), $hideSymbol);
    }
}
