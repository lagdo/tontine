@php
  $showAuction = !$session->pending && $pool->remit_auction;
@endphp
<div><b>@if(!$showAuction)-@else{!! $auction?->count ?? 0
  !!}&nbsp;/&nbsp;{!! $locale->formatMoney($auction?->amount ?? 0, false, false) !!}@endif
@if($pool->deposit_fixed)<br/>{{ !$showAuction ? '-' :
  $locale->formatMoney($collected->cashier->end - ($auction?->amount ?? 0), false, false) }}@endif</b></div>
