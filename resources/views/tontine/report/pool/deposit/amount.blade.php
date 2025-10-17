<div><b>@if(!$selected)-@else{!! $locale->formatMoney($collected->deposit->amount, false, false) !!}@endif</b>
@if($pool->deposit_fixed)<br/>{{ $locale->formatMoney($expected?->deposit->amount ?? 0, false, false) }}@endif</div>
