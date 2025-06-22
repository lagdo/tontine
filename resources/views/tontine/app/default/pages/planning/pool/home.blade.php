@php
  $rqPool = rq(Ajax\App\Planning\Pool\Pool::class);
  $rqPoolPage = rq(Ajax\App\Planning\Pool\PoolPage::class);
  $rqPoolCount = rq(Ajax\App\Planning\Pool\PoolCount::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ __('tontine.pool.titles.pools') }}</h2>
              </div>
              <div class="col-auto ml-auto">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->toggleFilter())><i class="fa fa-filter"></i></button>
                </div>
                <div class="btn-group ml-3" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->render())><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-header">
              <div class="row w-100">
                <div class="col-auto ml-auto pt-1" @jxnBind($rqPoolCount)>
                   @jxnHtml($rqPoolCount)
                </div>
              </div>
            </div>
            <div class="card-body" @jxnBind($rqPoolPage)>
            </div>
          </div>
