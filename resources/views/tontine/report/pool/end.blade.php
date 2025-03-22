<div><b>{!! $session->pending ? '-' : $locale->formatMoney($collected->cashier->end, false, false) !!}</b>
@if($pool->deposit_fixed)<br/>{{ $locale->formatMoney($expected?->cashier->end ?? 0, false, false) }}@endif</div>
