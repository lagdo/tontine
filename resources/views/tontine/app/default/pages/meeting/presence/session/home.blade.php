@php
  $rqPresence = Jaxon\rq(App\Ajax\Web\Meeting\Presence\Home::class);
  $rqSession = Jaxon\rq(App\Ajax\Web\Meeting\Presence\Session::class);
  $rqSessionPage = Jaxon\rq(App\Ajax\Web\Meeting\Presence\SessionPage::class);
  $jsBackHandler = Jaxon\jw()->showSmScreen('content-home-members', 'presence-sm-screens');
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">
@if (!$member)
                      {{ __('tontine.menus.presences') }}: {{ __('tontine.titles.sessions') }}
@else
                      {{ __('tontine.titles.presences', ['of' => $member->name]) }} ({{
                          $sessionCount - ($member->absences_count ?? 0) }}/{{ $sessionCount }})
@endif
                    </h2>
                  </div>
@if (!$member)
                  <div class="col-auto">
                    <button type="button" class="btn btn-primary" @jxnClick($rqPresence->exchange())><i class="fa fa-exchange-alt"></i></button>
                  </div>
@else
                  <div class="col-auto sm-screen-hidden">
                    <button type="button" class="btn btn-primary" @jxnClick($jsBackHandler)><i class="fa fa-arrow-left"></i></button>
                  </div>
@endif
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqSession->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div @jxnShow($rqSessionPage)>
                  </div>
                  <nav @jxnPagination($rqSessionPage)>
                  </nav>
                </div>
              </div>
