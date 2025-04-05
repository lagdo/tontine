@php
  $rqHostUser = rq(Ajax\App\Admin\User\Host\Host::class);
  $rqHostUserFunc = rq(Ajax\App\Admin\User\Host\HostFunc::class);
  $rqHostUserPage = rq(Ajax\App\Admin\User\Host\HostPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.invite.titles.hosts') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
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
