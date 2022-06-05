<?php

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

function localizedRoute(string $route, array $options = [])
{
    return LaravelLocalization::getLocalizedURL(
        LaravelLocalization::getCurrentLocale(), route($route, $options));
}

function localizedUrl(string $locale)
{
    return LaravelLocalization::getLocalizedURL($locale, null, [], true);
}

function currentLocalizedUrl()
{
    return localizedUrl(LaravelLocalization::getCurrentLocale());
}

function localizedIcon(string $locale)
{
    return $locale === 'en' ? 'us' : $locale;
}

function currentLocalizedIcon()
{
    return localizedIcon(LaravelLocalization::getCurrentLocale());
}
