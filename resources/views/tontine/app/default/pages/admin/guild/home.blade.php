@php
  $rqGuild = rq(Ajax\App\Admin\Guild\Guild::class);
  $rqGuildFunc = rq(Ajax\App\Admin\Guild\GuildFunc::class);
  $rqGuildPage = rq(Ajax\App\Admin\Guild\GuildPage::class);
  $rqGuestGuild = rq(Ajax\App\Admin\Guest\Guild::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.titles.guilds') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqGuild->home())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqGuildFunc->add())><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqGuildPage)>
            </div>
          </div>

@if ($hasGuestGuilds)
          <div @jxnBind($rqGuestGuild)>
            @jxnHtml($rqGuestGuild)
          </div>
@endif
