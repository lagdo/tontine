                <div class="section-body">
                  <div class="row align-items-center">
                    <div class="col">
                      <h2 class="section-title">{!! $tontine->name !!}</h2>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-save-guest-tontine-access"><i class="fa fa-save"></i></button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card shadow">
                  <div class="card-body">
                    <form class="form-horizontal" role="form" id="guest-tontine-access-form">
                      <div class="module-body">
                        <div class="form-group row">
                          <div class="col-md-12">{{ __('tontine.menus.tontine') }}</div>
                          <div class="col-md-11 offset-md-1">
                            {!! Form::checkbox('access[tontine][members]', '1', $access['tontine']['members'] ?? false) !!}
                            {!! Form::label('', __('tontine.menus.members'), ['class' => 'form-check-label']) !!}
                          </div>
                          <div class="col-md-11 offset-md-1">
                            {!! Form::checkbox('access[tontine][categories]', '1', $access['tontine']['categories'] ?? false) !!}
                            {!! Form::label('', __('tontine.menus.categories'), ['class' => 'form-check-label']) !!}
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col-md-12">{{ __('tontine.menus.planning') }}</div>
                          <div class="col-md-11 offset-md-1">
                            {!! Form::checkbox('access[planning][sessions]', '1', $access['planning']['sessions'] ?? false) !!}
                            {!! Form::label('', __('tontine.menus.sessions'), ['class' => 'form-check-label']) !!}
                          </div>
                          <div class="col-md-11 offset-md-1">
                            {!! Form::checkbox('access[planning][pools]', '1', $access['planning']['pools'] ?? false) !!}
                            {!! Form::label('', __('tontine.menus.pools'), ['class' => 'form-check-label']) !!}
                          </div>
                          <div class="col-md-11 offset-md-1">
                            {!! Form::checkbox('access[planning][subscriptions]', '1', $access['planning']['subscriptions'] ?? false) !!}
                            {!! Form::label('', __('tontine.menus.subscriptions'), ['class' => 'form-check-label']) !!}
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col-md-12">{!! __('tontine.menus.meeting') !!}</div>
                          <div class="col-md-11 offset-md-1">
                            {!! Form::checkbox('access[meeting][sessions]', '1', $access['meeting']['sessions'] ?? false) !!}
                            {!! Form::label('', __('tontine.menus.sessions'), ['class' => 'form-check-label']) !!}
                          </div>
                          <div class="col-md-11 offset-md-1">
                            {!! Form::checkbox('access[meeting][presences]', '1', $access['meeting']['presences'] ?? false) !!}
                            {!! Form::label('', __('tontine.menus.presences'), ['class' => 'form-check-label']) !!}
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
