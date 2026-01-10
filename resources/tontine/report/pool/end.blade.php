<div>
  <div><b>{!! !$selected ? '-' : $locale->formatMoney($collected->cashier->end, false, false) !!}</b></div>
@if($pool->deposit_fixed)
  <div>{{ $locale->formatMoney($expected?->cashier->end ?? 0, false, false) }}</div>
@endif
</div>
