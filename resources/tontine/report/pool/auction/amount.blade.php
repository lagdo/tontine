@php
  $showAuction = $selected && $pool->remit_auction;
@endphp
<div>
  <div><b>{!! !$showAuction ? '-' : $locale->formatMoney($auction?->amount ?? 0, false, false) !!}</b></div>
@if ($pool->deposit_fixed)
  <div><b>{{ !$showAuction ? '-' : $locale->formatMoney($collected->cashier->end - ($auction?->amount ?? 0), false, false) }}</b></div>
@endif
</div>
