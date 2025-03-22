<b>{!! $session->pending ? '-' : $locale->formatMoney($collected->cashier->recv, false, false) !!}</b>
@if($pool->deposit_fixed)<br/>{{ $locale->formatMoney($expected->cashier->recv, false, false) }}@endif
