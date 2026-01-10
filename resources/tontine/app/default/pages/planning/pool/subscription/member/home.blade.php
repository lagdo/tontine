@php
  $rqMember = rq(Ajax\App\Planning\Pool\Subscription\Member::class);
  $rqMemberPage = rq(Ajax\App\Planning\Pool\Subscription\MemberPage::class);
  $rqMemberCounter = rq(Ajax\App\Planning\Pool\Subscription\MemberCounter::class);
  $rqPool = rq(Ajax\App\Planning\Pool\Pool::class);
@endphp
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col-auto">
                    <div class="section-title mt-0 mb-0">{{ __('tontine.pool.titles.subscriptions') }}</div>
                    <div class="section-subtitle">{!! $pool->title !!}</div>
                  </div>
                  <div class="col-auto ml-auto">
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqPool->render())><i class="fa fa-arrow-left"></i></button>
                    </div>
                    <div class="btn-group ml-3" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqMember->toggleFilter())><i class="fa fa-filter"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqMember->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow mb-4">
                <div class="card-header">
                  <div class="row w-100">
                    <div class="col">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-subscription-members-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqMember
                            ->search(jq('#txt-subscription-members-search')->val()))><i class="fa fa-search"></i></button>
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
                <!-- Data tables -->
                <div class="card-body" @jxnBind($rqMemberPage)>
                </div>
              </div>
