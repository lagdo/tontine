@include('tontine.app.default.pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $rqDisbursement = rq(Ajax\App\Meeting\Session\Cash\Disbursement::class);
@endphp
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row">
                <div class="col-md-12" id="meeting-disbursements" @jxnBind($rqDisbursement)>
                </div>
              </div>
            </div>
          </div>
