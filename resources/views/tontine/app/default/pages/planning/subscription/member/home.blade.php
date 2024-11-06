@php
  $rqMember = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Member::class);
  $rqMemberPage = Jaxon\rq(App\Ajax\Web\Planning\Subscription\MemberPage::class);
  $rqMemberCounter = Jaxon\rq(App\Ajax\Web\Planning\Subscription\MemberCounter::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.pool.titles.members')
                      }} (<span @jxnShow($rqMemberCounter)>@jxnHtml($rqMemberCounter)</span>)</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqMember->filter())><i class="fa fa-filter"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqMember->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col">
                    <div class="input-group">
                      {!! $htmlBuilder->text('search', '')->id('txt-subscription-members-search')
                        ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                      <div class="input-group-append">
                        <button type="button" class="btn btn-primary" @jxnClick($rqMember->search(Jaxon\jq('#txt-subscription-members-search')->val()))><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body" @jxnShow($rqMemberPage)>
                </div>
              </div>
