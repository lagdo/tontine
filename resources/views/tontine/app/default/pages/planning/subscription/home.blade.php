@inject('locale', 'Siak\Tontine\Service\LocaleService')
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title" id="subscriptions-pool-name">{{ __('tontine.pool.titles.subscriptions') }}</h2>
              </div>
              <div class="col-auto">
                <div class="input-group">
                  {{ Form::select('pool_id', $pools, $poolId, ['class' => 'form-control', 'id' => 'select-pool']) }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" id="btn-pool-select"><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="row sm-screen-selector mb-3" id="pool-subscription-sm-screens">
              <div class="col-12">
                <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                  <button data-target="pool-subscription-members" type="button" class="btn btn-primary">
                    {{ __('tontine.pool.titles.members') }}
                  </button>
                  <button data-target="pool-subscription-sessions" type="button" class="btn btn-outline-primary">
                    {{ __('tontine.pool.titles.sessions') }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
            </div>
          </div>
