<div><b>@if(!$selected)-@else{!! $locale->formatMoney($collected->remitment->amount, false, false) !!}@endif</b>
@if($pool->deposit_fixed)<br/>{{ $locale->formatMoney($expected?->remitment->amount ?? 0, false, false) }}@endif</div>
