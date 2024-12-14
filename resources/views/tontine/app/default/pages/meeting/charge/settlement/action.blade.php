@if ($charge->is_active && !$charge->is_variable)
@php
  $rqSettlement = rq(Ajax\App\Meeting\Session\Charge\Fixed\Settlement::class);
@endphp
@if (!$session->closed)
@if ($settlementCount < $billCount)
<a role="link" tabindex="0" @jxnClick($rqSettlement->addAllSettlements())><i class="fa fa-toggle-off"></i></a>
@elseif ($billCount > 0)
<a role="link" tabindex="0" @jxnClick($rqSettlement->delAllSettlements())><i class="fa fa-toggle-on"></i></a>
@endif
@else
<i class="fa fa-toggle-{{ $settlementCount < $billCount ? 'off' : 'on' }}"></i>
@endif
@endif
