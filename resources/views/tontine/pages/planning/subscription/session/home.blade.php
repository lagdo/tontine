                  <div class="row align-items-center">
                    <div class="col">
                      <h2 class="section-title">{{ $pool->title }} - {{ __('tontine.pool.titles.sessions') }}</h2>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-subscription-sessions-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>

                  <!-- Data tables -->
                  <div class="table-responsive" id="pool-subscription-sessions-page">
                  </div> <!-- End table -->
