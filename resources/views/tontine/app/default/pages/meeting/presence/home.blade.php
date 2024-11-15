@php
  $rqSession = Jaxon\rq(Ajax\App\Meeting\Presence\Session::class);
  $rqMember = Jaxon\rq(Ajax\App\Meeting\Presence\Member::class);
  $rqAtLeft = !$exchange ? $rqSession : $rqMember;
  $rqAtRight = $exchange ? $rqSession : $rqMember;
@endphp
          <div class="row" id="presence-sm-screens">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" @jxnShow($rqAtLeft)>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" @jxnShow($rqAtRight)>
            </div>
          </div>
