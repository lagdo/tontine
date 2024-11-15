@if (!$session->closed)
@php
  $rqPool = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\Pool::class);
@endphp
@if ($depositCount < $receivableCount)
<a role="link" @jxnClick($rqPool->addAllDeposits())><i class="fa fa-toggle-off"></i></a>
@else
<a role="link" @jxnClick($rqPool->delAllDeposits())><i class="fa fa-toggle-on"></i></a>
@endif
@else
<i class="fa fa-toggle-{{ $depositCount < $receivableCount ? 'off' : 'on' }}"></i>
@endif
