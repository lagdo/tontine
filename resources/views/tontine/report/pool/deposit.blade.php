<b>@if($session->pending)-@else{!! $collected->deposit->count !!}&nbsp;/&nbsp;{!!
  $locale->formatMoney($collected->deposit->amount, false, false) !!}@endif</b>
@if($pool->deposit_fixed)<br/>{{ $expected?->deposit->count ?? 0 }}&nbsp;/&nbsp;{{
  $locale->formatMoney($expected?->deposit->amount ?? 0, false, false) }}@endif
