@php
  $rqStartSession = rq(Ajax\App\Planning\Pool\Session\StartSession::class);
  $rqStartSessionPage = rq(Ajax\App\Planning\Pool\Session\StartSessionPage::class);
  $rqStartSessionTitle = rq(Ajax\App\Planning\Pool\Session\StartSessionTitle::class);
  $rqStartSessionAction = rq(Ajax\App\Planning\Pool\Session\StartSessionAction::class);
  $rqEndSession = rq(Ajax\App\Planning\Pool\Session\EndSession::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.pool_round.titles.start') }}</h2>
                  </div>
                  <div class="col-auto sm-screen-hidden">
                    <button type="button" class="btn btn-primary" @jxnClick(js('Tontine')
                      ->showSmScreen('content-planning-pools', 'pool-sm-screens'))><i class="fa fa-arrow-left"></i></button>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqEndSession->render())><i class="fa fa-exchange-alt"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqStartSession->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0" @jxnBind($rqStartSessionTitle)></div>
                    </div>
                    <div class="col-auto" @jxnBind($rqStartSessionAction)>
                    </div>
                  </div>
                  <div @jxnBind($rqStartSessionPage)>
                  </div>
                </div>
              </div>
