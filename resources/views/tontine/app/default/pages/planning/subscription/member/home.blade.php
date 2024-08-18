              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.pool.titles.subscriptions') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="input-group">
                      {{ $htmlBuilder->select('pool_id', $pools, $pool->id)->id('select-pool')
                        ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') }}
                      <div class="input-group-append">
                        <button type="button" class="btn btn-primary" id="btn-pool-select"><i class="fa fa-arrow-right"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col">
                    <div class="input-group">
                      {!! $htmlBuilder->text('search', '')->id('txt-subscription-members-search')
                        ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                      <div class="input-group-append">
                        <button type="button" class="btn btn-primary" id="btn-subscription-members-search"><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
                      <button type="button" class="btn btn-primary" id="btn-subscription-members-filter"><i class="fa fa-filter"></i></button>
                      <button type="button" class="btn btn-primary" id="btn-subscription-members-refresh"><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="pool-subscription-members-page">
                  </div> <!-- End table -->
                </div>
              </div>
