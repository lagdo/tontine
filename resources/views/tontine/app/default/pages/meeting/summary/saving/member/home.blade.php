@php
  $searchValue = jq('#txt-fee-member-search')->val();
  $rqMember = rq(Ajax\App\Meeting\Summary\Saving\Member::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Summary\Saving\MemberPage::class);
  $rqMemberTotal = rq(Ajax\App\Meeting\Summary\Saving\MemberTotal::class);
  $rqSaving = rq(Ajax\App\Meeting\Summary\Saving\Saving::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! $fund->title !!}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSaving->render())><i class="fa fa-arrow-left"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-8">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-fee-member-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqMember->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-4" @jxnBind($rqMemberTotal)>
                    </div>
                  </div>
                  <div @jxnBind($rqMemberPage)>
                  </div>
