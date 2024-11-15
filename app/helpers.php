<?php

use Illuminate\Database\Eloquent\Model;
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

function paymentLink(?Model $payment, string $name, bool $sessionIsClosed)
{
    $icon = '<i class="fa fa-toggle-off"></i>';
    $linkClass = "btn-add-$name";
    if(($payment))
    {
        $icon = '<i class="fa fa-toggle-on"></i>';
        $linkClass = "btn-del-$name";
    }

    return $sessionIsClosed ? $icon :
        '<a role="link" tabindex="0" class="' . $linkClass . '">' . $icon . '</a>';
}
