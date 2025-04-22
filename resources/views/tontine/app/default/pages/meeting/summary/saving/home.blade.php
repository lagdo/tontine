@php
  $selectFundId = pm()->select('savings-fund-id')->toInt();
  $rqSaving = rq(Ajax\App\Meeting\Summary\Saving\Saving::class);
  $rqSavingPage = rq(Ajax\App\Meeting\Summary\Saving\SavingPage::class);
  $rqSavingCount = rq(Ajax\App\Meeting\Summary\Saving\SavingCount::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col" @jxnBind($rqSavingCount)>
                    </div>
                    <div class="col-auto">
                      <div class="input-group">
                        {!! $html->select('fund', $funds, $fundId)->id('savings-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqSaving->fund($selectFundId))><i class="fa fa-caret-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div @jxnBind($rqSavingPage)>
                  </div>
