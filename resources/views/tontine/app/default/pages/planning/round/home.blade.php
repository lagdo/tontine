          <div class="row" id="round-sm-screens">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-home-rounds">
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.titles.rounds') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group">
                      <button type="button" class="btn btn-primary" id="btn-show-select">
                        <i class="fa fa-check-square"></i> {{ __('tontine.actions.choose') }}
                      </button>
                    </div>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group">
                      <button type="button" class="btn btn-primary" id="btn-round-refresh"><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" id="btn-round-create"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="content-page-rounds">
                  </div> <!-- End table -->
                </div>
              </div>
            </div>

            <div class="col-md-6 col-sm-12 sm-screen" id="content-home-sessions">
            </div>
          </div>
