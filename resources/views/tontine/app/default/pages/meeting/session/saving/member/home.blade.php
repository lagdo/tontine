@php
  $searchValue = jq('#txt-saving-member-search')->val();
  $rqMember = rq(Ajax\App\Meeting\Session\Saving\Member::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Session\Saving\MemberPage::class);
  $rqMemberTotal = rq(Ajax\App\Meeting\Session\Saving\MemberTotal::class);
  $rqSaving = rq(Ajax\App\Meeting\Session\Saving\Saving::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{!! $fund->title !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSaving->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqMember->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>

@if ($session->id === $fund->start_sid)
@php
  $savingAmount = jq('#txt-saving-amount')->val();
  $rqAmountFunc = rq(Ajax\App\Meeting\Session\Saving\AmountFunc::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-md-6">
                      <div class="float-right py-2 font-weight-bold">{{ __('meeting.saving.labels.start_amount') }}</div>
                    </div>
                    <div class="col-md-6">
                      <div class="input-group">
                        {!! $html->text('amount', $locale->getMoneyValue($fund->start_amount))
                          ->id('txt-saving-amount')->class('form-control')
                          ->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqAmountFunc->saveStartAmount($savingAmount))><i class="fa fa-save"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
@endif
@if ($session->id === $fund->end_sid)
@php
  $savingAmount = jq('#txt-saving-amount')->val();
  $rqAmountFunc = rq(Ajax\App\Meeting\Session\Saving\AmountFunc::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-md-6">
                      <div class="float-right py-2 font-weight-bold">{{ __('meeting.saving.labels.end_amount') }}</div>
                    </div>
                    <div class="col-md-6">
                      <div class="input-group">
                        {!! $html->text('amount', $locale->getMoneyValue($fund->end_amount))
                          ->id('txt-saving-amount')->class('form-control')
                          ->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqAmountFunc->saveEndAmount($savingAmount))><i class="fa fa-save"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
@endif

                  <div class="row mb-2">
                    <div class="col-md-8">
                      <div class="input-group">
                        {!! $html->text('search', '')
                          ->id('txt-saving-member-search')->class('form-control')
                          ->attribute('style', 'height:36px; padding:5px 15px;') !!}
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
