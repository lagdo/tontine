@php
  $rqSession = rq(Ajax\App\Planning\Pool\Session\Session::class);
  $rqSessionCounter = rq(Ajax\App\Planning\Pool\Session\SessionCounter::class);
  $rqSessionPage = rq(Ajax\App\Planning\Pool\Session\SessionPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.pool.titles.sessions') }}</h2>
                  </div>
                  <div class="col-auto sm-screen-hidden">
                    <button type="button" class="btn btn-primary" @jxnClick(js('Tontine')
                      ->showSmScreen('content-planning-pools', 'pool-sm-screens'))><i class="fa fa-arrow-left"></i></button>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqSession->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0" @jxnBind($rqSessionCounter)>@jxnHtml($rqSessionCounter)</div>
                    </div>
                  </div>
                  <div @jxnBind($rqSessionPage)>
                  </div>
                </div>
              </div>
