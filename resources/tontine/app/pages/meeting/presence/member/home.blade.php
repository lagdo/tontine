@php
  $rqPresenceFunc = rq(Ajax\App\Meeting\Presence\PresenceFunc::class);
  $rqMember = rq(Ajax\App\Meeting\Presence\Member::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Presence\MemberPage::class);
@endphp
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col-auto">
                    <h2 class="section-title">
@if (!$session)
                      {{ __('tontine.menus.presences') }}: {{ __('tontine.titles.members') }}
@else
                      {{ __('tontine.titles.presences', ['of' => $session->title]) }} ({{
                        $memberCount - ($session->absences_count ?? 0) }}/{{ $memberCount }})
@endif
                    </h2>
                  </div>
                  <div class="col-auto ml-auto">
@if (!$session)
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
                      <button type="button" class="btn btn-primary" @jxnClick($rqMember->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-header">
                  <div class="row w-100">
                    <div class="col-auto">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-presence-members-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqMember
                            ->search(jq('#txt-presence-members-search')->val()))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto ml-auto" style="width:40px;">&nbsp;</div>
                  </div>
                </div>
                <div class="card-body" @jxnBind($rqMemberPage)>
                </div>
              </div>
