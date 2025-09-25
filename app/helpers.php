<?php

use Illuminate\Database\Eloquent\Model;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

if(!function_exists('localizedRoute'))
{
    function localizedRoute(string $route, array $options = [])
    {
        return LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(), route($route, $options));
    }
}

if(!function_exists('localizedUrl'))
{
    function localizedUrl(string $locale)
    {
        return LaravelLocalization::getLocalizedURL($locale, null, [], true);
    }
}

if(!function_exists('currentLocalizedUrl'))
{
    function currentLocalizedUrl()
    {
        return localizedUrl(LaravelLocalization::getCurrentLocale());
    }
}

if(!function_exists('localizedIcon'))
{
    function localizedIcon(string $locale)
    {
        return $locale === 'en' ? 'us' : $locale;
    }
}

if(!function_exists('currentLocalizedIcon'))
{
    function currentLocalizedIcon()
    {
        return localizedIcon(LaravelLocalization::getCurrentLocale());
    }
}

if(!function_exists('paymentLink'))
{
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
}
