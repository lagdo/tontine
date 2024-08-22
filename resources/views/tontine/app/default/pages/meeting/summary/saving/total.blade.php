@inject('locale', 'Siak\Tontine\Service\LocaleService')
@if ($savingCount > 0){{ $savingCount }} / {{ $locale->formatMoney($savingTotal, true) }}@endif
