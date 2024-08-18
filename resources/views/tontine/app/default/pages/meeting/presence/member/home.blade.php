              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">
@if (!$session)
                      {{ __('tontine.menus.presences') }}: {{ __('tontine.titles.members') }}
@else
                      {{ __('tontine.titles.presences', ['of' => $session->title]) }} ({{
                        $memberCount - ($session->absents_count ?? 0) }}/{{ $memberCount }})
@endif
                    </h2>
                  </div>
@if (!$session)
                  <div class="col-auto">
                    <button type="button" class="btn btn-primary" id="btn-presence-exchange"><i class="fa fa-exchange-alt"></i></button>
                  </div>
@else
                  <div class="col-auto sm-screen-hidden">
                    <button type="button" class="btn btn-primary" id="btn-presence-sessions-back"><i class="fa fa-arrow-left"></i></button>
                  </div>
@endif
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group">
                      <button type="button" class="btn btn-primary" id="btn-presence-members-refresh"><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col">&nbsp;</div>
                  <div class="col-auto">
                    <div class="input-group">
                      {!! $htmlBuilder->text('search', '')->id('txt-presence-members-search')
                        ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                      <div class="input-group-append">
                        <button type="button" class="btn btn-primary" id="btn-presence-members-search"><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="content-page-members">
                  </div> <!-- End table -->
                </div>
              </div>
