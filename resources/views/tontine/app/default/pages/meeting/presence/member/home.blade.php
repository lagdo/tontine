@php
  $rqPresenceFunc = rq(Ajax\App\Meeting\Presence\PresenceFunc::class);
  $rqMember = rq(Ajax\App\Meeting\Presence\Member::class);
  $rqMemberFunc = rq(Ajax\App\Meeting\Presence\MemberFunc::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Presence\MemberPage::class);
  $jsBackHandler = js('Tontine')->showSmScreen('content-presence-left', 'presence-sm-screens');
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">
@if (!$session)
                      {{ __('tontine.menus.presences') }}: {{ __('tontine.titles.members') }}
@else
                      {{ __('tontine.titles.presences', ['of' => $session->title]) }} ({{
                        $memberCount - ($session->absents_count ?? 0) }}/{{ $memberCount }})
@endif
                    </h2>
                  </div>
@if (!$session)
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
                      <button type="button" class="btn btn-primary" @jxnClick($rqMember->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-header">
                  <div class="row w-100">
                    <div class="col">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-presence-members-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqMemberFunc->search(jq('#txt-presence-members-search')->val()))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto" style="width:40px;">&nbsp;</div>
                  </div>
                </div>
                <div class="card-body" @jxnBind($rqMemberPage)>
                </div>
              </div>
