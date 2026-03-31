<div>
  <div><b>@if(!$selected)-@else{!! $locale->formatMoney($collected->remitment->amount, false, false) !!}@endif</b></div>
@if($pool->deposit_fixed)
  <div>{{ $locale->formatMoney($expected?->remitment->amount ?? 0, false, false) }}</div>
@endif
</div>
