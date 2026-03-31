<div>
  <div><b>{!! !$selected ? '-' : $locale->formatMoney($collected->cashier->recv, false, false) !!}</b></div>
@if($pool->deposit_fixed)
  <div>{{ $locale->formatMoney($expected?->cashier->recv ?? 0, false, false) }}</div>
@endif
</div>
