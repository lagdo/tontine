@php
  $rqGuestUser = rq(Ajax\User\Guest\Guest::class);
  $rqGuestUserPage = rq(Ajax\User\Guest\GuestPage::class);
@endphp
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col-auto">
                    <h2 class="section-title">{{ __('tontine.invite.titles.guests') }}</h2>
                  </div>
                  <div class="col-auto ml-auto">
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqGuestUser->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow mb-4">
                <div class="card-body" @jxnBind($rqGuestUserPage)>
                </div>
              </div>
