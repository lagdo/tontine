@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqHome = rq(Ajax\App\Planning\Pool\Pool::class);
  $rqPool = rq(Ajax\App\Planning\Pool\Session\Pool::class);
  $rqPoolPage = rq(Ajax\App\Planning\Pool\Session\PoolPage::class);
  $rqPoolSection = rq(Ajax\App\Planning\Pool\Session\PoolSection::class);
@endphp
          <div class="row" id="pool-sm-screens">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-planning-pools">
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.titles.sessions') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqHome->render())><i class="fa fa-arrow-left"></i></button>
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
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-planning-sessions" @jxnBind($rqPoolSection)>
            </div>
          </div>
