@php
  $searchValue = jq('#txt-saving-member-search')->val();
  $rqMember = rq(Ajax\App\Meeting\Session\Saving\Member::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Session\Saving\MemberPage::class);
  $rqMemberTotal = rq(Ajax\App\Meeting\Session\Saving\MemberTotal::class);
  $rqSaving = rq(Ajax\App\Meeting\Session\Saving\Saving::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0 mb-0">{!! __('meeting.titles.savings') !!}</div>
                      <div class="section-subtitle">{!! $fund->title !!}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSaving->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqMember->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-md-8">
                      <div class="input-group">
                        {!! $html->text('search', '')
                          ->id('txt-saving-member-search')->class('form-control')
                          ->attribute('style', 'height:36px; padding:5px 5px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqMember->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4" @jxnBind($rqMemberTotal)>
                    </div>
                  </div>
                  <div @jxnBind($rqMemberPage)>
                  </div>
