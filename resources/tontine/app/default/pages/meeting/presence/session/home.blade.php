@php
  $rqPresenceFunc = rq(Ajax\App\Meeting\Presence\PresenceFunc::class);
  $rqSession = rq(Ajax\App\Meeting\Presence\Session::class);
  $rqSessionPage = rq(Ajax\App\Meeting\Presence\SessionPage::class);
@endphp
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col-auto">
                    <h2 class="section-title">
@if (!$member)
                      {{ __('tontine.menus.presences') }}: {{ __('tontine.titles.sessions') }}
@else
                      {{ __('tontine.titles.presences', ['of' => $member->name]) }} ({{
                          $sessionCount - ($member->absences_count ?? 0) }}/{{ $sessionCount }})
@endif
                    </h2>
                  </div>
                  <div class="col-auto ml-auto">
@if (!$member)
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqPresenceFunc->exchange())><i class="fa fa-exchange-alt"></i></button>
                    </div>
@else
                    <div class="btn-group sm-screen-hidden" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick(jo('tontine')
                        ->showSmScreen('content-presence-left', 'presence-sm-screens'))><i class="fa fa-arrow-left"></i></button>
                    </div>
@endif
                    <div class="btn-group" role="group">
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
