@php
  $rqSession = rq(Ajax\App\Planning\Financial\Session::class);
  $rqSessionPage = rq(Ajax\App\Planning\Financial\SessionPage::class);
  $rqSessionAction = rq(Ajax\App\Planning\Financial\SessionAction::class);
  $rqSessionHeader = rq(Ajax\App\Planning\Financial\SessionHeader::class);
  $rqPool = rq(Ajax\App\Planning\Financial\Pool::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.titles.sessions') }}</h2>
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-primary" @jxnClick($rqPool->render())><i class="fa fa-arrow-left"></i></button>
              </div>
              <div class="col-auto" @jxnBind($rqSessionAction)>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-header" @jxnBind($rqSessionHeader)>
            </div>
            <div class="card-body" @jxnBind($rqSessionPage)>
            </div>
          </div>
