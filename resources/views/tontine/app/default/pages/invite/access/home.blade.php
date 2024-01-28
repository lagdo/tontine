              <div class="section-body">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.invite.titles.access') }} :: {!! $guest->name !!}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group" aria-label="">
                      <button type="button" class="btn btn-primary" id="btn-host-invites-back"><i class="fa fa-arrow-left"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col">{{ __('tontine.titles.tontine') }}</div>
                    <div class="col-auto">
                      <div class="input-group">
                        {{ Form::select('tontine_id', $tontines, 0, ['class' => 'form-control', 'id' => 'select-invite-tontine']) }}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-select-invite-tontine"><i class="fa fa-arrow-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div id="content-host-invite-access">
              </div>
