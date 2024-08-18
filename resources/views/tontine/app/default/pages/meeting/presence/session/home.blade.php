              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">
@if (!$member)
                      {{ __('tontine.menus.presences') }}: {{ __('tontine.titles.sessions') }}
@else
                      {{ __('tontine.titles.presences', ['of' => $member->name]) }} ({{
                          $sessionCount - ($member->absences_count ?? 0) }}/{{ $sessionCount }})
@endif
                    </h2>
                  </div>
@if (!$member)
                  <div class="col-auto">
                    <button type="button" class="btn btn-primary" id="btn-presence-exchange"><i class="fa fa-exchange-alt"></i></button>
                  </div>
@else
                  <div class="col-auto sm-screen-hidden">
                    <button type="button" class="btn btn-primary" id="btn-presence-members-back"><i class="fa fa-arrow-left"></i></button>
                  </div>
@endif
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group"row>
                      <button type="button" class="btn btn-primary" id="btn-presence-sessions-refresh"><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="content-page-sessions">
                  </div> <!-- End table -->
                </div>
              </div>
