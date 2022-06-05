              <div class="section-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <h2 class="section-title">{{ $fund->title }} - {{ __('tontine.menus.members') }}</h2>
                  </div>
                  <div class="col">
                    <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                      <button type="button" class="btn btn-primary" id="btn-subscription-filter"><i class="fa fa-filter"></i></button>
                      <button type="button" class="btn btn-primary" id="btn-subscription-refresh"><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="subscription-page">
                  </div> <!-- End table -->
                </div>
              </div>
