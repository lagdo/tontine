@inject('locale', 'Siak\Tontine\Service\LocaleService')
@if ($savingCount > 0){{ $savingCount }}<br/>{{ $locale->formatMoney($savingTotal, true) }}@endif
