@php
  $rqCharge = rq(Ajax\App\Meeting\Session\Charge\Libre\Fee::class);
  $rqSettlementFunc = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementFunc::class);
  $rqSettlementPage = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementPage::class);
  $rqTotal = rq(Ajax\App\Meeting\Session\Charge\Settlement\Total::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">
                        {{ $charge->name }} - {{ __('meeting.titles.settlements') }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqSettlementFunc->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col">
                    </div>
                    <div class="col-auto" @jxnBind($rqTotal, 'libre') style="padding: 7px 15px 7px 5px;">
                    </div>
                  </div>
                  <div @jxnBind($rqSettlementPage)>
                  </div>
