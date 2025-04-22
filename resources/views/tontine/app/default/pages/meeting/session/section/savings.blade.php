@php
  $rqSaving = rq(Ajax\App\Meeting\Session\Saving\Saving::class);
  $rqLoan = rq(Ajax\App\Meeting\Session\Credit\Loan\Loan::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title">{{ $session->title }}: {!! __("meeting.actions.savings") !!}</h2>
              </div>
              <div class="col-auto ml-auto">
@include('tontine::pages.report.session.action.exports', ['sessionId' => $session->id])
@include('tontine::pages.meeting.session.section.action')
              </div>
            </div>
          </div>

          <div class="row sm-screen-selector mt-2 mb-1" id="session-savings-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="content-session-loans" type="button" class="btn btn-primary">
                  {!! __('meeting.titles.loans') !!}
                </button>
                <button data-target="content-session-savings" type="button" class="btn btn-outline-primary">
                  {!! __('meeting.titles.savings') !!}
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-session-loans">
              <div class="card shadow mb-2">
                <div class="card-body" @jxnBind($rqLoan)>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-session-savings">
              <div class="card shadow mb-2">
                <div class="card-body" @jxnBind($rqSaving)>
                </div>
              </div>
            </div>
          </div>
