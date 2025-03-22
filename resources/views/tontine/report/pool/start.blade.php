<b>{!! $session->pending ? '-' : $locale->formatMoney($collected->cashier->start, false, false) !!}</b>
@if($pool->deposit_fixed)<br/>{{ $locale->formatMoney($expected->cashier->start, false, false) }}@endif
