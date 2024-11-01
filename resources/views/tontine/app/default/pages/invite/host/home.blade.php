@php
  $rqHostInvite = Jaxon\rq(App\Ajax\Web\Tontine\Invite\Host::class);
  $rqHostInvitePage = Jaxon\rq(App\Ajax\Web\Tontine\Invite\HostPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.invite.titles.hosts') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqHostInvite->render())><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqHostInvite->add())><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div @jxnShow($rqHostInvitePage)>
                  </div> <!-- End table -->
                  <nav @jxnPagination($rqHostInvitePage)>
                  </nav>
                </div>
              </div>
