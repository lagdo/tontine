@php
  $rqSession = rq(Ajax\App\Planning\Pool\Session\Pool\Session::class);
  $rqSessionCounter = rq(Ajax\App\Planning\Pool\Session\Pool\SessionCounter::class);
  $rqSessionPage = rq(Ajax\App\Planning\Pool\Session\Pool\SessionPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.pool.titles.sessions') }}</h2>
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
