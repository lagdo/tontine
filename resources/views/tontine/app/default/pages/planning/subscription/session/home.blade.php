              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.pool.titles.sessions') }}
                    (<span id="pool-subscription-sessions-total">{{ $total }}</span>)</h2>
                  </div>
@if ($pool->remit_planned)
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" id="btn-subscription-beneficiaries">{{
                        __('tontine.subscription.titles.beneficiaries') }}</i></button>
                      <button type="button" class="btn btn-primary" id="btn-subscription-planning">{{
                        __('tontine.subscription.titles.planning') }}</i></button>
                    </div>
                  </div>
@endif
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" id="btn-subscription-sessions-refresh"><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="pool-subscription-sessions-page">
                  </div> <!-- End table -->
                </div>
              </div>
