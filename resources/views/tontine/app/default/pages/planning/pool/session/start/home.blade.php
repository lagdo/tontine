@php
  $rqStartSession = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\StartSession::class);
  $rqStartSessionPage = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\StartSessionPage::class);
  $rqStartSessionTitle = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\StartSessionTitle::class);
  $rqStartSessionAction = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\StartSessionAction::class);
  $rqEndSession = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\EndSession::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.pool_round.titles.start') }}</h2>
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
