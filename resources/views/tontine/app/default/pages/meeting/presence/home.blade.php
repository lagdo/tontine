              <div class="section-body">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.menus.presences') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group" aria-label="">
                      <button type="button" class="btn btn-primary" id="btn-presence-exchange"><i class="fa fa-exchange-alt"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 col-sm-12" id="content-home-{{ !$exchange ? 'sessions' : 'members' }}">
                    </div>
                    <div class="col-md-6 col-sm-12" id="content-home-{{ !$exchange ? 'members' : 'sessions' }}">
                    </div>
                  </div>
                </div>
              </div>
