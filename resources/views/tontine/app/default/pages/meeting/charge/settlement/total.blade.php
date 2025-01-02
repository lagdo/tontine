@inject('locale', 'Siak\Tontine\Service\LocaleService')
@if ($billCount > 0)
({{ $settlementCount }}/{{ $billCount }})
@endif
@if ($settlementCount > 0){!! $locale->formatMoney($settlementAmount, true) !!}
@endif
