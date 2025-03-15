@php
  $rqFixedFee = rq(Ajax\App\Meeting\Session\Charge\Fixed\Fee::class);
  $rqLibreFee = rq(Ajax\App\Meeting\Session\Charge\Libre\Fee::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}: {!! __("meeting.actions.charges") !!}</h2>
              </div>
              <div class="col">
@include('tontine::pages.report.session.action.exports', ['sessionId' => $session->id])
@include('tontine::pages.meeting.session.section.action')
              </div>
            </div>
          </div>

          <div class="row sm-screen-selector mt-2 mb-1" id="session-charges-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="content-session-fees-fixed" type="button" class="btn btn-primary">
                  {!! __('meeting.charge.titles.fixed') !!}
                </button>
                <button data-target="content-session-fees-libre" type="button" class="btn btn-outline-primary">
                  {!! __('meeting.charge.titles.variable') !!}
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-session-fees-fixed">
              <div class="card shadow mb-2">
                <div class="card-body" @jxnBind($rqFixedFee)>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-session-fees-libre">
              <div class="card shadow mb-2">
                <div class="card-body" @jxnBind($rqLibreFee)>
                </div>
              </div>
            </div>
          </div>
