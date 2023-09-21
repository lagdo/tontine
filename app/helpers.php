<?php

use Illuminate\Database\Eloquent\Model;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Session;

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

function paymentLink(?Model $payment, string $name, bool $disableLink)
{
    $icon = '<i class="fa fa-toggle-off"></i>';
    $linkClass = "btn-add-$name";
    if(($payment))
    {
        $icon = '<i class="fa fa-toggle-on"></i>';
        $linkClass = "btn-del-$name";
        if(($payment->online))
        {
            $icon .= '&nbsp;<i class="fa fa-link"></i>';
            // Different class for online payment
            $linkClass = 'online-payment';
        }
    }

    return $disableLink ? $icon :
        '<a href="javascript:void(0)" class="' . $linkClass . '">' . $icon . '</a>';
}

function debtIsEditable(Debt $debt, Session $session)
{
    // Refunded
    if($debt->refund !== null)
    {
        // Editable if refunded in the current session
        return $debt->refund->session_id === $session->id;
    }
    if($debt->is_interest && !$debt->loan->fixed_interest)
    {
        // Cannot refund the interest debt before the principal.
        return $debt->loan->principal_debt->refund !== null;
    }
    // Not yet refunded
    $lastRefund = $debt->partial_refunds->sortByDesc('session.start_at')->first();
    return !$lastRefund || $lastRefund->session->start_at < $session->start_at;
}
