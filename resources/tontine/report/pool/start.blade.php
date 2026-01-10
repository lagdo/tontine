<div>
  <div><b>{!! !$selected ? '-' : $locale->formatMoney($collected->cashier->start, false, false) !!}</b></div>
@if($pool->deposit_fixed)
  <div>{{ $locale->formatMoney($expected?->cashier->start ?? 0, false, false) }}</div>
@endif
</div>
