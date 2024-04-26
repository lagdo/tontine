@inject('locale', 'Siak\Tontine\Service\LocaleService')
{{ $depositCount }}@if ($depositAmount > 0) / {{ $locale->formatMoney($depositAmount, true) }} @endif
