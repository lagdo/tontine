@include('tontine::pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $rqTotalRefund = rq(Ajax\App\Meeting\Session\Refund\Total\Refund::class);
  $rqPartialRefund = rq(Ajax\App\Meeting\Session\Refund\Partial\Refund::class);
@endphp
          <div class="row sm-screen-selector mt-2 mb-1" id="session-refunds-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="content-session-total-refunds" type="button" class="btn btn-primary">
                  {!! __('meeting.refund.titles.final') !!}
                </button>
                <button data-target="content-session-partial-refunds" type="button" class="btn btn-outline-primary">
                  {!! __('meeting.refund.titles.partial') !!}
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-session-total-refunds">
              <div class="card shadow mb-2">
                <div class="card-body" @jxnBind($rqTotalRefund)>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-session-partial-refunds">
              <div class="card shadow mb-2">
                <div class="card-body" @jxnBind($rqPartialRefund)>
                </div>
              </div>
            </div>
          </div>
