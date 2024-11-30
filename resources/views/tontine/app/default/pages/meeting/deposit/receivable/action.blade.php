@if (!$session->closed)
@if ($pool->deposit_fixed)
@php
  $rqReceivable = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\Receivable::class);
@endphp
@if ($depositCount < $receivableCount)
<a role="link" tabindex="0" @jxnClick($rqReceivable->addAllDeposits())><i class="fa fa-toggle-off"></i></a>
@else
<a role="link" tabindex="0" @jxnClick($rqReceivable->delAllDeposits())><i class="fa fa-toggle-on"></i></a>
@endif
@endif
@else
<i class="fa fa-toggle-{{ $depositCount < $receivableCount ? 'off' : 'on' }}"></i>
@endif
