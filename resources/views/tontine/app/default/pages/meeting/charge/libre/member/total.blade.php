@inject('locale', 'Siak\Tontine\Service\LocaleService')
@if ($settlementCount > 0)
({{ $settlementCount }}) {!! $locale->formatMoney($settlementAmount, true) !!}
@endif