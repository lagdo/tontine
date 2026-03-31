<div>
  <div><b>@if(!$selected)-@else{!! $collected->deposit->count !!}@endif</b></div>
@if($pool->deposit_fixed)
  <div>{{ $expected?->deposit->count ?? 0 }}</div>
@endif
</div>
