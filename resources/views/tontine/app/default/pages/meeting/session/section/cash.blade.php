@include('tontine::pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $rqDisbursement = rq(Ajax\App\Meeting\Session\Cash\Disbursement::class);
@endphp
          <div class="card shadow mb-2">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12" id="content-session-disbursements" @jxnBind($rqDisbursement)>
                </div>
              </div>
            </div>
          </div>
