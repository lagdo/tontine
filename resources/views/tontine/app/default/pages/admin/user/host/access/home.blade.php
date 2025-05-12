@php
  $guildId = pm()->select('select-invite-guild');
  $rqHostAccessFunc = rq(Ajax\User\Host\AccessFunc::class);
  $rqHostGuildAccess = rq(Ajax\User\Host\GuildAccess::class);
  $rqHost = rq(Ajax\User\Host\Host::class);
@endphp
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col-auto">
                    <h2 class="section-title">{{ __('tontine.invite.titles.access') }} :: {!! $guest->name !!}</h2>
                  </div>
                  <div class="col-auto ml-auto">
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqHost->render())><i class="fa fa-arrow-left"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">{{ __('tontine.titles.tontine') }}</div>
                    <div class="col-12">
                      <div class="input-group">
                        {{ $html->select('guild_id', $guilds, 0)->class('form-control')->id('select-invite-guild') }}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqHostAccessFunc->guild($guildId))><i class="fa fa-caret-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div @jxnBind($rqHostGuildAccess) id="content-host-invite-access">
              </div>
