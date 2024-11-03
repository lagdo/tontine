@php
  $rqSession = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Session::class);
  $rqSessionCounter = Jaxon\rq(App\Ajax\Web\Planning\Subscription\SessionCounter::class);
  $rqSessionPage = Jaxon\rq(App\Ajax\Web\Planning\Subscription\SessionPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.pool.titles.sessions')
                      }} (<span @jxnShow($rqSessionCounter)>@jxnHtml($rqSessionCounter)</span>)</h2>
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
                  <div @jxnShow($rqSessionPage)>
                  </div>
                  <nav @jxnPagination($rqSessionPage)>
                  </nav>
                </div>
              </div>
