@php
  $rqCharge = rq(Ajax\App\Meeting\Session\Charge\Libre\Fee::class);
  $rqSettlement = rq(Ajax\App\Meeting\Session\Charge\Libre\Settlement::class);
  $rqSettlementPage = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementPage::class);
  $rqSettlementFunc = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementFunc::class);
  $rqSettlementAll = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementAll::class);
  $rqTotal = rq(Ajax\App\Meeting\Session\Charge\Settlement\Total::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">
                        {{ __('meeting.titles.settlements') }}
                      </div>
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
                  <div class="row mb-2" style="padding: 7px 15px 7px 5px;">
                    <div class="col-auto">
                      {{ $charge->name }}
                    </div>
                    <div class="col-auto ml-auto" @jxnBind($rqTotal, 'libre')>
                    </div>
                  </div>
                  <div @jxnBind($rqSettlementPage)>
                  </div>
