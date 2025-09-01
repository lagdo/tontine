<span class="deposit-here">{{ $pool->paid_here
  }}</span><span>@if ($pool->paid_late > 0)+<span class="deposit-late">{{ $pool->paid_late
  }}</span>@endif</span><span>@if ($pool->paid_early > 0)+<span class="deposit-early">{{
  $pool->paid_early }}</span>@endif</span>/<span class="deposit-here">{{ $pool->recv_count
  }}</span>
@if ($pool->prev_late > 0 || $pool->next_early > 0)<br/>
  <span>@if ($pool->prev_late > 0)+<span class="deposit-late">{{ $pool->prev_late
  }}</span>@endif</span><span>@if ($pool->next_early > 0)+<span class="deposit-early">{{
  $pool->next_early }}</span>@endif</span>@endif
