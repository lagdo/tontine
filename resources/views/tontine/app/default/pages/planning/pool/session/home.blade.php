@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqHome = Jaxon\rq(Ajax\App\Planning\Pool\Pool::class);
  $rqPool = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool::class);
  $rqPoolPage = Jaxon\rq(Ajax\App\Planning\Pool\Session\PoolPage::class);
  $rqPoolSection = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\PoolSection::class);
@endphp
          {{-- <div class="row sm-screen-selector mb-3" id="pool-subscription-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="pool-subscription-members" type="button" class="btn btn-primary">
                  {{ __('tontine.titles.sessions') }}
                </button>
                <button data-target="pool-subscription-sessions" type="button" class="btn btn-outline-primary">
                  {{ __('tontine.pool.titles.sessions') }}
                </button>
              </div>
            </div>
          </div> --}}

          <div class="row" id="content-page">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active">
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
                <div class="card-body" @jxnShow($rqPoolPage)>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" @jxnShow($rqPoolSection)>
            </div>
          </div>
