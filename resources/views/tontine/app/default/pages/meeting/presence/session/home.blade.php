@php
  $rqPresenceFunc = rq(Ajax\App\Meeting\Presence\PresenceFunc::class);
  $rqSession = rq(Ajax\App\Meeting\Presence\Session::class);
  $rqSessionPage = rq(Ajax\App\Meeting\Presence\SessionPage::class);
  $jsBackHandler = js('Tontine')->showSmScreen('content-presence-left', 'presence-sm-screens');
@endphp
              <div class="section-body">
                <div class="row mb-2">
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
                    <button type="button" class="btn btn-primary" @jxnClick($rqPresenceFunc->exchange())><i class="fa fa-exchange-alt"></i></button>
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
                <div class="card-body" @jxnBind($rqSessionPage)>
                </div>
              </div>
