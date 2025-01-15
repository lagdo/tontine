@include('tontine.app.default.pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $rqFixedFee = rq(Ajax\App\Meeting\Session\Charge\Fixed\Fee::class);
  $rqLibreFee = rq(Ajax\App\Meeting\Session\Charge\Libre\Fee::class);
@endphp
          <div class="card shadow mb-4">
            <div class="card-body" id="session-charges">
              <div class="row sm-screen-selector mb-3" id="session-charges-sm-screens">
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
                <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-session-fees-fixed" @jxnBind($rqFixedFee)>
                </div>
                <div class="col-md-6 col-sm-12 sm-screen" id="content-session-fees-libre" @jxnBind($rqLibreFee)>
                </div>
              </div>
            </div>
          </div>
