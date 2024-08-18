                <div class="section-body">
                  <div class="row">
                    <div class="col">
                      <h2 class="section-title">{!! $tontine->name !!}</h2>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2" role="group">
                        <button type="button" class="btn btn-primary" id="btn-save-guest-tontine-access"><i class="fa fa-save"></i></button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card shadow">
                  <div class="card-body">
                    <form class="form-horizontal" role="form" id="guest-tontine-access-form">
                      <div class="row">
                        <div class="col-md-6 col-sm-12">
                          <div class="module-body">
                            <div class="form-group row">
                              <div class="col-md-12">{{ __('tontine.menus.tontine') }}</div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[tontine][members]', $access['tontine']['members'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.members'), '')->class('form-check-label') !!}
                              </div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[tontine][categories]', $access['tontine']['categories'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.categories'), '')->class('form-check-label') !!}
                              </div>
                            </div>
                            <div class="form-group row">
                              <div class="col-md-12">{{ __('tontine.menus.planning') }}</div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[planning][sessions]', $access['planning']['sessions'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.sessions'), '')->class('form-check-label') !!}
                              </div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[planning][pools]', $access['planning']['pools'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.pools'), '')->class('form-check-label') !!}
                              </div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[planning][subscriptions]', $access['planning']['subscriptions'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.subscriptions'), '')->class('form-check-label') !!}
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                          <div class="module-body">
                            <div class="form-group row">
                              <div class="col-md-12">{!! __('tontine.menus.report') !!}</div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[report][session]', $access['report']['session'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.session'), '')->class('form-check-label') !!}
                              </div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[report][round]', $access['report']['round'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.round'), '')->class('form-check-label') !!}
                              </div>
                            </div>
                            <div class="form-group row">
                              <div class="col-md-12">{!! __('tontine.menus.meeting') !!}</div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[meeting][sessions]', $access['meeting']['sessions'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.sessions'), '')->class('form-check-label') !!}
                              </div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[meeting][payments]', $access['meeting']['payments'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.payments'), '')->class('form-check-label') !!}
                              </div>
                              <div class="col-md-11 offset-md-1">
                                {!! $htmlBuilder->checkbox('access[meeting][presences]', $access['meeting']['presences'] ?? false, '1') !!}
                                {!! $htmlBuilder->label(__('tontine.menus.presences'), '')->class('form-check-label') !!}
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
