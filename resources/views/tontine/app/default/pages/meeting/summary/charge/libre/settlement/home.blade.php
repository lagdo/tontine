@php
  $rqCharge = rq(Ajax\App\Meeting\Summary\Charge\Libre\Fee::class);
  $rqSettlement = rq(Ajax\App\Meeting\Summary\Charge\Libre\Settlement::class);
  $rqSettlementPage = rq(Ajax\App\Meeting\Summary\Charge\Libre\SettlementPage::class);
  $rqTotal = rq(Ajax\App\Meeting\Summary\Charge\Settlement\Total::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">
                        {{ $charge->name }} - {{ __('meeting.titles.settlements') }}
                      </div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqSettlement->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-auto">
                    </div>
                    <div class="col-auto ml-auto" @jxnBind($rqTotal, 'libre') style="padding: 7px 15px 7px 5px;">
                    </div>
                  </div>
                  <div @jxnBind($rqSettlementPage)>
                  </div>
