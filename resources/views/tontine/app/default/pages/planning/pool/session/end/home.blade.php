@php
  $rqEndSession = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\EndSession::class);
  $rqEndSessionPage = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\EndSessionPage::class);
  $rqEndSessionTitle = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\EndSessionTitle::class);
  $rqEndSessionAction = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\EndSessionAction::class);
  $rqStartSession = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\StartSession::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.pool_round.titles.end') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqStartSession->render())><i class="fa fa-exchange-alt"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqEndSession->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0" @jxnBind($rqEndSessionTitle)></div>
                    </div>
                    <div class="col-auto" @jxnBind($rqEndSessionAction)>
                    </div>
                  </div>
                  <div @jxnBind($rqEndSessionPage)>
                  </div>
                </div>
              </div>
