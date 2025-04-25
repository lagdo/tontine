@php
  $rqPool = rq(Ajax\App\Planning\Pool\Pool::class);
  $rqPoolPage = rq(Ajax\App\Planning\Pool\PoolPage::class);
  $rqPoolFunc = rq(Ajax\App\Planning\Pool\PoolFunc::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.pool.titles.pools') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPoolFunc->filter())><i class="fa fa-filter"></i></button>
                </div>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->render())><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqPoolPage)>
            </div>
          </div>
