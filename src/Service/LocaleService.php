<?php

namespace Siak\Tontine\Service;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use Illuminate\Support\Collection;
use Mcamara\LaravelLocalization\LaravelLocalization;
use Rinvex\Country\CountryLoader;
use Siak\Tontine\Model\Guild;
use NumberFormatter;

use function array_merge;
use function route;
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

    public function __construct(private LaravelLocalization $localization,
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
        return $this->currency?->getName() ?? '';
    }

    /**
     * Get the currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol(): string
    {
        return $this->currency?->getSymbol() ?? '';
    }

    /**
     * @param bool $withEmpty
     *
     * @return array
     */
    public function getCountries(bool $withEmpty = true): array
    {
        $locale = $this->localization->getCurrentLocale();
        $countries = include($this->countriesDataDir . "/{$locale}/country.php");
        // Append an empty item to the array.
        return !$withEmpty ? $countries : array_merge(['' => ''], $countries);
    }

    /**
     * @param string $code
     *
     * @return array
     */
    public function getCountryCurrencies(string $code): array
    {
        $country = CountryLoader::country($code, false);
        $locale = $this->localization->getCurrentLocale();
        $localizedCurrencies = include($this->currenciesDataDir . "/{$locale}/currency.php");

        $currencies = [];
        foreach($country['currency'] ?? [] as $currency)
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
        $locale = $this->localization->getCurrentLocale();
        $localizedCountries = include $this->countriesDataDir . "/{$locale}/country.php";
        $localizedCurrencies = include $this->currenciesDataDir . "/{$locale}/currency.php";

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
     * Get the names of countries and currencies from a guild
     *
     * @param Guild $guild
     *
     * @return array
     */
    public function getNameFromGuild(Guild $guild): array
    {
        [$countries, $currencies] = $this->getNames([$guild->country_code], [$guild->currency_code]);
        return [$countries[$guild->country_code] ?? '', $currencies[$guild->currency_code] ?? ''];
    }

    /**
     * Get the names of countries and currencies from a collection of guilds
     *
     * @param Collection $guilds
     *
     * @return array
     */
    public function getNamesFromGuilds(Collection $guilds): array
    {
        $countryCodes = $guilds->pluck('country_code')->toArray();
        $currencyCodes = $guilds->pluck('currency_code')->toArray();
        return $this->getNames($countryCodes, $currencyCodes);
    }

    /**
     * @return string
     */
    private function _locale(): string
    {
        $locales = ['en' => 'en_GB', 'fr' => 'fr_FR'];
        return $locales[$this->localization->getCurrentLocale()] ?? 'en_GB';
    }

    /**
     * @return NumberFormatter
     */
    private function makeDecimalFormatter(): NumberFormatter
    {
        $formatter = new NumberFormatter($this->_locale(), NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS,
            $this->currency->getPrecision());
        return $formatter;
    }

    /**
     * @param bool $fixedPrecision
     *
     * @return NumberFormatter
     */
    private function decimalFormatter(bool $fixedPrecision = false): NumberFormatter
    {
        $this->formatter ??= $this->makeDecimalFormatter();
        $this->formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS,
            $fixedPrecision ? $this->currency->getPrecision() : 0);
        return $this->formatter;
    }

    /**
     * Get a formatted amount.
     *
     * @param int $amount
     * @param bool $showSymbol
     * @param bool $fixedPrecision
     *
     * @return string
     */
    public function formatMoney(int $amount, bool $showSymbol = false,
        bool $fixedPrecision = true): string
    {
        $money = new Money($amount, $this->currency);
        return $showSymbol ? $money->formatLocale($this->_locale()) :
            $this->decimalFormatter($fixedPrecision)->format($money->getValue());
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

    /**
     * Get the translated URL for a route
     *
     * @param string $name
     * @param array $attributes
     *
     * @return string
     */
    public function route(string $name, array $attributes = []): string
    {
        $locale = $this->localization->getCurrentLocale();
        return $this->localization->getLocalizedUrl($locale, route($name, $attributes));
    }
}
