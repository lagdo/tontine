{{-- span tags are used so we don't have to add spaces --}}
<span class="deposit-here">{{ $pool->paid_here
  }}</span><span>@if ($pool->paid_late > 0)+<span class="deposit-late">{{ $pool->paid_late
  }}</span>@endif</span><span>@if ($pool->paid_early > 0)+<span class="deposit-early">{{
  $pool->paid_early }}</span>@endif</span>/<span class="deposit-here">{{ $pool->recv_count
  }}</span><span>@if ($pool->prev_late > 0)+<span class="deposit-late">{{ $pool->prev_late
  }}</span>@endif</span><span>@if ($pool->next_early > 0)+<span class="deposit-early">{{
  $pool->next_early }}</span>@endif</span>
  @if ($pool->recv_amount > 0)<br/>{{ $locale->formatMoney($pool->recv_amount) }}@endif
