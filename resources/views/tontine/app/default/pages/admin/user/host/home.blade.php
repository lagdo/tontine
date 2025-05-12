@php
  $rqHostUser = rq(Ajax\User\Host\Host::class);
  $rqHostUserFunc = rq(Ajax\User\Host\HostFunc::class);
  $rqHostUserPage = rq(Ajax\User\Host\HostPage::class);
@endphp
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col-auto">
                    <h2 class="section-title">{{ __('tontine.invite.titles.hosts') }}</h2>
                  </div>
                  <div class="col-auto ml-auto">
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqHostUser->render())><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqHostUserFunc->add())><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div @jxnBind($rqHostUserPage)>
                  </div> <!-- End table -->
                </div>
              </div>
