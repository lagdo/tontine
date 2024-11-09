@include('tontine.app.default.pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $rqLoan = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\Loan::class);
  $rqRefund = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\Refund::class);
  $rqPartialRefund = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\Partial\Refund::class);
@endphp
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row sm-screen-selector mb-3" id="session-credits-sm-screens">
                <div class="col-12">
                  <div class="btn-group btn-group-sm btn-block" role="group">
                    <button data-target="meeting-loans-col" type="button" class="btn btn-primary">
                      {!! __('meeting.titles.loans') !!}
                    </button>
                    <button data-target="meeting-refunds" type="button" class="btn btn-outline-primary">
                      {!! __('meeting.titles.refunds') !!}
                    </button>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="meeting-loans-col">
                  <div id="meeting-loans" @jxnShow($rqLoan)>
                  </div>
                  <div id="meeting-partial-refunds" @jxnShow($rqPartialRefund)>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12 sm-screen" id="meeting-refunds" @jxnShow($rqRefund)>
                </div>
              </div>
            </div>
          </div>
