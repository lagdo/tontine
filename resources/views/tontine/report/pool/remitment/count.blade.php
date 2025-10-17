<div><b>@if(!$selected)-@else{!! $collected->remitment->count !!}@endif</b>
@if($pool->deposit_fixed)<br/>{{ $expected?->remitment->count ?? 0 }}@endif</div>
