@php
  $rqMember = rq(Ajax\App\Planning\Subscription\Member::class);
  $rqMemberFunc = rq(Ajax\App\Planning\Subscription\MemberFunc::class);
  $rqMemberPage = rq(Ajax\App\Planning\Subscription\MemberPage::class);
  $rqMemberCounter = rq(Ajax\App\Planning\Subscription\MemberCounter::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.pool.titles.subscriptions') }} :: {{ $pool->title }}</h2>
                  </div>
                  <div class="col-auto sm-screen-hidden">
                    <button type="button" class="btn btn-primary" @jxnClick(js('Tontine')
                      ->showSmScreen('content-subscription-pools', 'subscription-sm-screens'))><i class="fa fa-arrow-left"></i></button>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqMemberFunc->filter())><i class="fa fa-filter"></i></button>
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
                        {!! $html->text('search', '')->id('txt-subscription-members-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqMemberFunc->search(jq('#txt-subscription-members-search')->val()))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div style="width:40px; font-weight:bold; padding-top:5px;" @jxnBind($rqMemberCounter)>
                        @jxnHtml($rqMemberCounter)
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body" @jxnBind($rqMemberPage)>
                </div>
              </div>
