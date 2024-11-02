@include('tontine.app.default.pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $rqDisbursement = Jaxon\rq(App\Ajax\Web\Meeting\Session\Cash\Disbursement::class);
@endphp
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row">
                <div class="col-md-12" id="meeting-disbursements" @jxnShow($rqDisbursement)>
                </div>
              </div>
            </div>
          </div>
