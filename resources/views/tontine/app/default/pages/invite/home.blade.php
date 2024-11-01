@php
  $rqInvite = Jaxon\rq(App\Ajax\Web\Tontine\Invite\Invite::class);
  $rqHostInvite = Jaxon\rq(App\Ajax\Web\Tontine\Invite\Host::class);
  $rqGuestInvite = Jaxon\rq(App\Ajax\Web\Tontine\Invite\Guest::class);
@endphp
          <div class="row sm-screen-selector mb-3" id="invites-sm-screens">
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
          <div class="row">
            <div @jxnShow($rqHostInvite) class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-host-invites-home">
            </div>
            <div @jxnShow($rqGuestInvite) class="col-md-6 col-sm-12 sm-screen" id="content-guest-invites-home">
            </div>
          </div>
