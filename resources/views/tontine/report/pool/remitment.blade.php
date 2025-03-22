<b>@if($session->pending)-@else{!! $collected->remitment->count !!} / {!!
  $locale->formatMoney($collected->remitment->amount, false, false) !!}@endif</b>
@if($pool->deposit_fixed)<br/>{{ $expected->remitment->count }} / {{
  $locale->formatMoney($expected->remitment->amount, false, false) }}@endif
