@php
  $rqCharge = rq(Ajax\App\Meeting\Session\Charge\Libre\Fee::class);
  $rqSettlement = rq(Ajax\App\Meeting\Session\Charge\Libre\Settlement::class);
  $rqSettlementPage = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementPage::class);
  $rqSettlementFunc = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementFunc::class);
  $rqSettlementAll = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementAll::class);
  $rqSettlementTotal = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementTotal::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0 mb-0">{{ __('meeting.titles.settlements') }}</div>
                      <div class="section-subtitle">{{ $charge->name }}</div>
                    </div>
                    <div class="col-auto ml-auto" @jxnBind($rqSettlementAll)>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqSettlement->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-7">
                    </div>
                    <div class="col-3" @jxnBind($rqSettlementTotal)>
                    </div>
                  </div>
                  <div @jxnBind($rqSettlementPage)>
                  </div>
