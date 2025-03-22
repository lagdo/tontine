<b>@if($session->pending)-@else{!! $collected->deposit->count !!} / {!!
  $locale->formatMoney($collected->deposit->amount, false, false) !!}@endif</b>
@if($pool->deposit_fixed)<br/>{{ $expected->deposit->count }} / {{
  $locale->formatMoney($expected->deposit->amount, false, false) }}@endif
