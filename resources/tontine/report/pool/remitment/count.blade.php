<div>
  <div><b>@if(!$selected)-@else{!! $collected->remitment->count !!}@endif</b></div>
@if($pool->deposit_fixed)
  <div>{{ $expected?->remitment->count ?? 0 }}</div>
@endif
</div>
