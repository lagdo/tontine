                      <div class="row">
                        <div class="col">
                          <h2 class="section-title">
@if (!$session)
                            {{ __('tontine.titles.members') }}
@else
                            {{ __('tontine.titles.presences', ['of' => $session->title]) }} ({{
                              $memberCount - ($session->absents_count ?? 0) }}/{{ $memberCount }})
@endif
                          </h2>
                        </div>
@if (($session))
                        <div class="col-auto sm-screen-hidden">
                          <button type="button" class="btn btn-primary" id="btn-presence-sessions-back"><i class="fa fa-arrow-left"></i></button>
                        </div>
@endif
                        <div class="col-auto">
                          <div class="btn-group float-right" role="group" aria-label="">
                            <button type="button" class="btn btn-primary" id="btn-presence-members-refresh"><i class="fa fa-sync"></i></button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">&nbsp;</div>
                        <div class="col-auto">
                          <div class="input-group">
                            {!! Form::text('search', '', ['class' => 'form-control', 'id' => 'txt-presence-members-search']) !!}
                            <div class="input-group-append">
                              <button type="button" class="btn btn-primary" id="btn-presence-members-search"><i class="fa fa-search"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="table-responsive" id="content-page-members">
                      </div>
