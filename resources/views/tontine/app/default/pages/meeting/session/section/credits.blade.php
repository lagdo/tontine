@include('tontine.app.default.pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $rqLoan = rq(Ajax\App\Meeting\Session\Credit\Loan::class);
  $rqRefund = rq(Ajax\App\Meeting\Session\Credit\Refund::class);
  $rqPartialRefund = rq(Ajax\App\Meeting\Session\Credit\Partial\Refund::class);
@endphp
          <div class="card shadow mb-4">
            <div class="card-body" id="session-credits">
              <div class="row sm-screen-selector mb-3" id="session-credits-sm-screens">
                <div class="col-12">
                  <div class="btn-group btn-group-sm btn-block" role="group">
                    <button data-target="content-session-loans" type="button" class="btn btn-primary">
                      {!! __('meeting.titles.loans') !!}
                    </button>
                    <button data-target="content-session-refunds" type="button" class="btn btn-outline-primary">
                      {!! __('meeting.titles.refunds') !!}
                    </button>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-session-loans">
                  <div @jxnBind($rqLoan)>
                  </div>
                  <div @jxnBind($rqPartialRefund)>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12 sm-screen" id="content-session-refunds" @jxnBind($rqRefund)>
                </div>
              </div>
            </div>
          </div>
