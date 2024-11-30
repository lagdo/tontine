@php
  $tontineId = Jaxon\pm()->select('select-invite-tontine');
  $rqGuestAccess = Jaxon\rq(Ajax\App\Tontine\Invite\Guest\Access::class);
  $rqGuestAccessContent = Jaxon\rq(Ajax\App\Tontine\Invite\Guest\AccessContent::class);
  $rqInvite = Jaxon\rq(Ajax\App\Tontine\Invite\Invite::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.invite.titles.access') }} :: {!! $guest->name !!}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqInvite->render())><i class="fa fa-arrow-left"></i></button>
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
                          <button type="button" class="btn btn-primary" @jxnClick($rqGuestAccess->tontine($tontineId))><i class="fa fa-arrow-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div @jxnBind($rqGuestAccessContent) id="content-host-invite-access">
              </div>
