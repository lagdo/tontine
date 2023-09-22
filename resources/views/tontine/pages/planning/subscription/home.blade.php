@inject('locale', 'Siak\Tontine\Service\LocaleService')
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.pool.titles.subscriptions') }}</h2>
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
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row">
                <div class="col-md-6 col-sm-12" id="pool-subscription-members">
                </div>
                <div class="col-md-6 col-sm-12" id="pool-subscription-sessions">
                </div>
              </div>
            </div>
          </div>
