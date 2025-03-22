<div><b>@if($session->pending)-@else{!! $collected->deposit->count !!}@endif</b>
@if($pool->deposit_fixed)<br/>{{ $expected?->deposit->count ?? 0 }}@endif</div>
