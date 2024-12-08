@php
  $tontineId = Jaxon\pm()->select('select-invite-tontine');
  $rqHostAccess = Jaxon\rq(Ajax\App\Admin\User\Host\Access::class);
  $rqHostAccessContent = Jaxon\rq(Ajax\App\Admin\User\Host\AccessContent::class);
  $rqHost = Jaxon\rq(Ajax\App\Admin\User\Host::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.invite.titles.access') }} :: {!! $guest->name !!}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqHost->render())><i class="fa fa-arrow-left"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow">
                <div class="card-body">
                  <div class="row">
                    <div class="col">{{ __('tontine.titles.tontine') }}</div>
                    <div class="col-auto">
                      <div class="input-group">
                        {{ $htmlBuilder->select('tontine_id', $tontines, 0)->class('form-control')->id('select-invite-tontine') }}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqHostAccess->tontine($tontineId))><i class="fa fa-arrow-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div @jxnBind($rqHostAccessContent) id="content-host-invite-access">
              </div>
