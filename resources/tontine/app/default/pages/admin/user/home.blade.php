@php
  $rqHostUser = rq(Ajax\App\Admin\User\Host\Host::class);
  $rqGuestUser = rq(Ajax\App\Admin\User\Guest\Guest::class);
@endphp
            <div class="row sm-screen-selector mt-2 mb-1" id="invites-sm-screens">
              <div class="col-12">
                <div class="btn-group btn-group-sm btn-block" role="group">
                  <button data-target="content-host-invites-home" type="button" class="btn btn-primary">
                    {{ __('tontine.invite.titles.hosts') }}
                  </button>
                  <button data-target="content-guest-invites-home" type="button" class="btn btn-outline-primary">
                    {{ __('tontine.invite.titles.guests') }}
                  </button>
                </div>
              </div>
            </div>

            <div class="row" id="invites-content">
              <div @jxnBind($rqHostUser) class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-host-invites-home">
              </div>
              <div @jxnBind($rqGuestUser) class="col-md-6 col-sm-12 sm-screen" id="content-guest-invites-home">
              </div>
            </div>
