@php
  $searchValue = jq('#txt-fee-member-search')->val();
  $rqMember = rq(Ajax\App\Meeting\Session\Charge\Libre\Member::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Session\Charge\Libre\MemberPage::class);
  $rqMemberTotal = rq(Ajax\App\Meeting\Session\Charge\Libre\MemberTotal::class);
  $rqCharge = rq(Ajax\App\Meeting\Session\Charge\Libre\Fee::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ $charge->name }}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqMember->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-1">
                    <div class="col-auto">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-fee-member-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqMember->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto ml-auto" @jxnBind($rqMemberTotal) style="padding: 7px 5px;">
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="input-group input-group-sm float-right mb-1 mr-0 pr-0">
                        <div class="input-group-prepend">
                          <div class="input-group-text" style="height:36px;">
                            {!! $html->checkbox('', $paid, '1')->id('check-fee-libre-paid') !!}
                          </div>
                        </div>
                        {!! $html->label(__('common.labels.paid'), '')->class('form-control')
                          ->attribute('style', 'height:36px; padding:5px 15px;') !!}
                      </div>
                    </div>
                  </div>
                  <div @jxnBind($rqMemberPage)>
                  </div>
