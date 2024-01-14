                      <div class="row align-items-center">
                        <div class="col">
                          <h2 class="section-title">{{ __('tontine.titles.presences', ['session' => $session->title]) }}</h2>
                        </div>
                        <div class="col-auto">
                          <div class="btn-group float-right" role="group" aria-label="">
                            <button type="button" class="btn btn-primary" id="btn-presence-members-refresh"><i class="fa fa-sync"></i></button>
                          </div>
                        </div>
                      </div>

                      <div class="table-responsive" id="content-page-members">
                      </div>
