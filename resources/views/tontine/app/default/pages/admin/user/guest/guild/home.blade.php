@php
  $rqGuild = rq(Ajax\User\Guest\Guild::class);
  $rqGuildPage = rq(Ajax\User\Guest\GuildPage::class);
@endphp
            <div class="section-body">
              <div class="row mb-2">
                <div class="col">
                  <h2 class="section-title">{{ __('tontine.invite.titles.organisations') }}</h2>
                </div>
                <div class="col-auto">
                  <div class="btn-group float-right ml-2" role="group">
                    <button type="button" class="btn btn-primary" @jxnClick($rqGuild->render())><i class="fa fa-sync"></i></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card shadow mb-4">
              <div class="card-body" @jxnBind($rqGuildPage)>
              </div>
            </div>
