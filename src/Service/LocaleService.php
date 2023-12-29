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
     * @var NumberFormatter
     */
    private $formatter = null;

    public function __construct(private string $locale,
        private string $countriesDataDir, private string $currenciesDataDir)
    {}

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
     * Get the currency name
     *
     * @return string
     */
    public function getCurrencyName(): string
    {
        return $this->currency->getName();
    }

    /**
     * @return array
     */
    public function getCountries(): array
    {
        return include($this->countriesDataDir . "/{$this->locale}/country.php");
    }

    /**
     * @param string $code
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
    public function getNameFromTontine(Tontine $tontine): array
    {
        [$countries, $currencies] = $this->getNames([$tontine->country_code], [$tontine->currency_code]);
        return [$countries[$tontine->country_code] ?? '', $currencies[$tontine->currency_code] ?? ''];
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
        $countryCodes = $tontines->pluck('country_code')->toArray();
        $currencyCodes = $tontines->pluck('currency_code')->toArray();
        return $this->getNames($countryCodes, $currencyCodes);
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
        if($this->formatter === null)
        {
            $this->formatter = new NumberFormatter($this->_locale(), NumberFormatter::DECIMAL);
            $precision = $this->currency->getPrecision();
            $this->formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $precision);
            $this->formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $precision);
        }
        return $this->formatter;
    }

    /**
     * Get a formatted amount.
     *
     * @param int $amount
     * @param bool $showSymbol
     *
     * @return string
     */
    public function formatMoney(int $amount, bool $showSymbol = true): string
    {
        $money = new Money($amount, $this->currency);
        return $showSymbol ? $money->formatLocale($this->_locale()) :
            $this->decimalFormatter()->format($money->getValue());
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
