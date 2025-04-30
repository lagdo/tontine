@if ($settlementCount > 0)
({{ $settlementCount }}) {!! $locale->formatMoney($settlementAmount) !!}
@endif