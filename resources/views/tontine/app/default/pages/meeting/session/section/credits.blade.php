@include('tontine.app.default.pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $rqLoan = rq(Ajax\App\Meeting\Session\Credit\Loan::class);
@endphp
          <div class="card shadow mb-2">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12" id="content-session-loans" @jxnBind($rqLoan)>
                </div>
              </div>
            </div>
          </div>
