<div><b>{!! $session->pending ? '-' : $locale->formatMoney($collected->cashier->recv, false, false) !!}</b>
@if($pool->deposit_fixed)<br/>{{ $locale->formatMoney($expected?->cashier->recv ?? 0, false, false) }}@endif</div>
