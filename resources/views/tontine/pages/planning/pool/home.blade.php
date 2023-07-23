          <div class="row">
            <div class="col-md-6 col-sm-12">
              <div class="section-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <h2 class="section-title">{{ __('tontine.menus.pools') }}</h2>
                  </div>
@if (!$tontine->is_libre)
                  <div class="col">
                    <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                      <button type="button" class="btn btn-primary" id="btn-refresh"><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" id="btn-create"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
@endif
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="pool-page">
                  </div> <!-- End table -->
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12" id="subscription-home">
            </div>
          </div>
