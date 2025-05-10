@php
  $rqProfit = rq(Ajax\App\Meeting\Session\Saving\Profit::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title">{{ $session->title }}: {!! __("meeting.actions.credits") !!}</h2>
              </div>
              <div class="col-auto ml-auto">
@include('tontine::pages.report.session.action.exports', ['sessionId' => $session->id])
@include('tontine::pages.meeting.session.section.action')
              </div>
            </div>
          </div>

          <div class="card shadow mb-2">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12" @jxnBind($rqProfit)>
                </div>
              </div>
            </div>
          </div>
