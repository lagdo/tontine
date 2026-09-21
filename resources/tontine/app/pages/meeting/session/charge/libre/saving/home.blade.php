@php
  $searchValue = jq('#txt-saving-settlements-search')->val();
  $rqCharge = rq(Ajax\App\Meeting\Session\Charge\Libre\Fee::class);
  $rqSaving = rq(Ajax\App\Meeting\Session\Charge\Libre\Saving::class);
  $rqSavingPage = rq(Ajax\App\Meeting\Session\Charge\Libre\SavingPage::class);
  $rqSavingAll = rq(Ajax\App\Meeting\Session\Charge\Libre\SavingAll::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0 mb-0">{{ __('meeting.titles.settlements') }}</div>
                      <div class="section-subtitle">{{ $charge->name }}</div>
                    </div>
                    <div class="col-auto ml-auto" @jxnBind($rqSavingAll)>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqSaving->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-6">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-saving-settlements-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqSaving->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="input-group">
                        {!! $html->select('fund_id', $funds, '')->id('settlement-saving-fund')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') !!}
                      </div>
                    </div>
                  </div>
                  <div @jxnBind($rqSavingPage)>
                  </div>
