@php
  $selectFundId = Jaxon\select('profits-fund-id')->toInt();
  $rqFund = rq(Ajax\App\Meeting\Session\Profit\Fund::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0 mb-0">{!! __('meeting.titles.profits') !!}</div>
                      <div class="section-subtitle" id="content-report-profits-fund"></div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="input-group">
                        {!! $html->select('fund', $funds, $fund?->id ?? 0)->id('profits-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqProfit
                            ->fund($selectFundId))><i class="fa fa-caret-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div @jxnBind($rqFund)>
                  </div>
