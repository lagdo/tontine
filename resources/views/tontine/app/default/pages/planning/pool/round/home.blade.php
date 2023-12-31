          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.pool_round.titles.sessions', ['pool' => $pool->title]) }}</h2>
              </div>
              <div class="col-auto" id="pool-round-actions">
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body">
              <form id="round-form">
                <div class="row">
                  <div class="col-md-6 col-sm-12">
                    <div class="row align-items-center">
                      <div class="col">
                        <h2 class="section-title" id="pool-round-start-session-title"></h2>
                      </div>
                      <div class="col-auto">
                        <div class="btn-group float-right" role="group" aria-label="">
                          <button type="button" class="btn btn-primary" id="btn-show-start-session-page"><i class="fa fa-arrow-circle-down"></i></button>
                        </div>
                      </div>
                    </div>

                    <!-- Data tables -->
                    <div class="table-responsive" id="pool-round-sessions-start">
                    </div> <!-- End table -->
                  </div>
                  <div class="col-md-6 col-sm-12">
                    <div class="row align-items-center">
                      <div class="col">
                        <h2 class="section-title" id="pool-round-end-session-title"></h2>
                      </div>
                      <div class="col-auto">
                        <div class="btn-group float-right" role="group" aria-label="">
                          <button type="button" class="btn btn-primary" id="btn-show-end-session-page"><i class="fa fa-arrow-circle-down"></i></button>
                        </div>
                      </div>
                    </div>

                    <!-- Data tables -->
                    <div class="table-responsive" id="pool-round-sessions-end">
                    </div> <!-- End table -->
                  </div>
                </div>
              </form>
            </div>
          </div>
