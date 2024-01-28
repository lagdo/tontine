          <div class="row">
            <div class="col-md-6 col-sm-12" id="content-host-invites-home">
              <div class="section-body">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.invite.titles.hosts') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                      <button type="button" class="btn btn-primary" id="btn-host-invites-refresh"><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" id="btn-host-invite-create"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="content-host-invites-page">
                  </div> <!-- End table -->
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12" id="content-guest-invites-home">
              <div class="section-body">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.invite.titles.guests') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                      <button type="button" class="btn btn-primary" id="btn-guest-invites-refresh"><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="content-guest-invites-page">
                  </div> <!-- End table -->
                </div>
              </div>
            </div>
          </div>
