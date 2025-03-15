@php
  $rqDisbursement = rq(Ajax\App\Meeting\Session\Cash\Disbursement::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}: {!! __("meeting.actions.cash") !!}</h2>
              </div>
              <div class="col">
@include('tontine::pages.report.session.action.exports', ['sessionId' => $session->id])
@include('tontine::pages.meeting.session.section.action')
              </div>
            </div>
          </div>

          <div class="card shadow mb-2">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12" id="content-session-disbursements" @jxnBind($rqDisbursement)>
                </div>
              </div>
            </div>
          </div>
