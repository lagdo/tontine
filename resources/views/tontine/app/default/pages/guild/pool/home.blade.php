@php
  $rqPool = rq(Ajax\App\Guild\Pool\Pool::class);
  $rqPoolFunc = rq(Ajax\App\Guild\Pool\PoolFunc::class);
  $rqPoolPage = rq(Ajax\App\Guild\Pool\PoolPage::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ __('tontine.titles.pools') }}</h2>
              </div>
              <div class="col-auto ml-auto">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->render())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqPoolFunc->showIntro())><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqPoolPage)>
            </div>
          </div>
