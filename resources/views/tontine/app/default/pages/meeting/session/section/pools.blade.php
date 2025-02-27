@include('tontine::pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $rqDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Deposit::class);
  $rqRemitment = rq(Ajax\App\Meeting\Session\Pool\Remitment\Remitment::class);
@endphp
          <div class="row sm-screen-selector mt-2 mb-1" id="session-pools-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="content-session-deposits" type="button" class="btn btn-primary">
                  {!! __('meeting.titles.deposits') !!}
                </button>
                <button data-target="content-session-remitments" type="button" class="btn btn-outline-primary">
                  {!! __('meeting.titles.remitments') !!}
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-session-deposits">
              <div class="card shadow mb-2">
                <div class="card-body" @jxnBind($rqDeposit)>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-session-remitments">
              <div class="card shadow mb-2">
                <div class="card-body" @jxnBind($rqRemitment)>
                </div>
              </div>
            </div>
          </div>
