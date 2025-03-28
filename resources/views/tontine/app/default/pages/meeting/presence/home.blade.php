@php
  $rqSession = rq(Ajax\App\Meeting\Presence\Session::class);
  $rqMember = rq(Ajax\App\Meeting\Presence\Member::class);
  $rqAtLeft = !$exchange ? $rqSession : $rqMember;
  $rqAtRight = $exchange ? $rqSession : $rqMember;
@endphp
          <div class="row" id="presence-sm-screens">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-presence-left" @jxnBind($rqAtLeft)>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-presence-right" @jxnBind($rqAtRight)>
            </div>
          </div>
