@php
  $rqGuestUser = Jaxon\rq(Ajax\App\Admin\User\Guest\Guest::class);
  $rqGuestUserPage = Jaxon\rq(Ajax\App\Admin\User\Guest\GuestPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.invite.titles.guests') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqGuestUser->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow mb-4">
                <div class="card-body" @jxnBind($rqGuestUserPage)>
                </div>
              </div>
