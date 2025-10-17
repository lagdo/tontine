<div><b>{!! !$selected ? '-' : $locale->formatMoney($collected->cashier->start, false, false) !!}</b>
@if($pool->deposit_fixed)<br/>{{ $locale->formatMoney($expected?->cashier->start ?? 0, false, false) }}@endif</div>
