@php
  $showAuction = $selected && $pool->remit_auction;
@endphp
<div><b>{!! !$showAuction ? '-' : $locale->formatMoney($auction?->amount ?? 0, false, false) !!}
@if($pool->deposit_fixed)<br/>{{ !$showAuction ? '-' : $locale
  ->formatMoney($collected->cashier->end - ($auction?->amount ?? 0), false, false) }}@endif</b></div>
