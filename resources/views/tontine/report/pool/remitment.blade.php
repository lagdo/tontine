<b>@if($session->pending)-@else{!! $collected->remitment->count !!}&nbsp;/&nbsp;{!!
  $locale->formatMoney($collected->remitment->amount, false, false) !!}@endif</b>
@if($pool->deposit_fixed)<br/>{{ $expected?->remitment->count ?? 0 }}&nbsp;/&nbsp;{{
  $locale->formatMoney($expected?->remitment->amount ?? 0, false, false) }}@endif
