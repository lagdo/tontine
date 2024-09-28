@php
  $rqPool = Jaxon\rq(App\Ajax\Web\Planning\Pool::class);
  $rqPoolPage = Jaxon\rq(App\Ajax\Web\Planning\PoolPage::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.menus.pools') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->home())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->showIntro())><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body">
              <div @jxnShow($rqPoolPage)>
              </div>
              <nav @jxnPagination($rqPoolPage)>
              </nav>
            </div>
          </div>
