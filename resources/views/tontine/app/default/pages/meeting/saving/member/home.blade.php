@php
  $searchValue = jq('#txt-fee-member-search')->val();
  $rqMember = rq(Ajax\App\Meeting\Session\Saving\Member::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Session\Saving\MemberPage::class);
  $rqSaving = rq(Ajax\App\Meeting\Session\Saving\Saving::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! !$fund ? __('tontine.fund.labels.default') : $fund->title !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSaving->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqMember->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col">
                      <div class="input-group">
                        {!! $htmlBuilder->text('search', '')->id('txt-fee-member-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqMember->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto" id="meeting-saving-members-total">
                    </div>
                  </div>
                  <div @jxnBind($rqMemberPage)>
                  </div>
