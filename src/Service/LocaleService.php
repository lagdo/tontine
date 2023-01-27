<?php

namespace Siak\Tontine\Service;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use Illuminate\Support\Collection;
use Rinvex\Country\CountryLoader;
use Siak\Tontine\Model\Tontine;
use NumberFormatter;

use function strtoupper;

class LocaleService
{
    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $countriesDataDir;

    /**
     * @var string
     */
    private $currenciesDataDir;

    public function __construct(string $locale, string $countriesDataDir, string $currenciesDataDir)
    {
        $this->locale = $locale;
        $this->countriesDataDir = $countriesDataDir;
        $this->currenciesDataDir = $currenciesDataDir;
    }

    /**
     * Set the currency to be used for money
     *
     * @param string $currency
     *
     * @return void
     */
    public function setCurrency(string $currency)
    {
        $this->currency = new Currency(strtoupper($currency));
    }

    /**
     * @return array
     */
    public function getCountries(): array
    {
        return ['' => ''] + include($this->countriesDataDir . "/{$this->locale}/country.php");
    }

    /**
     * @param string code
     *
     * @return array
     */
    public function getCountryCurrencies(string $code): array
    {
        $country = CountryLoader::country($code, false);
        $localizedCurrencies = include($this->currenciesDataDir . "/{$this->locale}/currency.php");

        $currencies = [];
        foreach($country['currency'] as $currency)
        {
            $currencyCode = $currency['iso_4217_code'];
            $currencies[$currencyCode] = $localizedCurrencies[$currencyCode];
        }
        return $currencies;
    }

    /**
     * Get the names of countries and currencies
     *
     * @param array $countries
     * @param array $currencies
     *
     * @return array
     */
    public function getNames(array $countries, array $currencies): array
    {
        $localizedCountries = include($this->countriesDataDir . "/{$this->locale}/country.php");
        $localizedCurrencies = include($this->currenciesDataDir . "/{$this->locale}/currency.php");

        $countryNames = [];
        foreach($countries as $code)
        {
            if(isset($localizedCountries[$code]))
            {
                $countryNames[$code] = $localizedCountries[$code];
            }
        }
        $currencyNames = [];
        foreach($currencies as $code)
        {
            if(isset($localizedCurrencies[$code]))
            {
                $currencyNames[$code] = $localizedCurrencies[$code];
            }
        }
        return [$countryNames, $currencyNames];
    }

    /**
     * Get the names of countries and currencies from a tontine model
     *
     * @param Tontine $tontine
     *
     * @return array
     */
    public function getNamesFromTontine(Tontine $tontine): array
    {
        return $this->getNames([$tontine->country_code], [$tontine->currency_code]);
    }

    /**
     * Get the names of countries and currencies from a collection of tontines
     *
     * @param Collection $tontines
     *
     * @return array
     */
    public function getNamesFromTontines(Collection $tontines): array
    {
        return $this->getNames($tontines->pluck('country_code')->toArray(), $tontines->pluck('currency_code')->toArray());
    }

    /**
     * @return string
     */
    private function _locale(): string
    {
        $locales = ['en' => 'en_GB', 'fr' => 'fr_FR'];
        return $locales[$this->locale] ?? 'en_GB';
    }

    /**
     * @return NumberFormatter
     */
    private function decimalFormatter(): NumberFormatter
    {
        $formatter = new NumberFormatter($this->_locale(), NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $this->currency->getPrecision());

        return $formatter;
    }

    /**
     * Get a formatted amount.
     *
     * @param int $amount
     * @param bool $hideSymbol
     *
     * @return string
     */
    public function formatMoney(int $amount, bool $hideSymbol = false): string
    {
        $money = new Money($amount, $this->currency);
        if($hideSymbol)
        {
            return $this->decimalFormatter()->format($money->getValue());
        }
        return $money->formatLocale($this->_locale());
    }

    /**
     * @param float $amount
     *
     * @return int
     */
    public function convertMoneyToInt(float $amount): int
    {
        return (int)(new Money($amount, $this->currency, true))->getAmount();
    }

    /**
     * @param int $amount
     *
     * @return float
     */
    public function getMoneyValue(int $amount): float
    {
        return (new Money($amount, $this->currency, false))->getValue();
    }
}
