@php
  $rqCharge = rq(Ajax\App\Planning\Charge\Charge::class);
  $rqChargePage = rq(Ajax\App\Planning\Charge\ChargePage::class);
  $rqChargeCount = rq(Ajax\App\Planning\Charge\ChargeCount::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ __('tontine.charge.titles.charges') }}</h2>
              </div>
              <div class="col-auto ml-auto">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqCharge->toggleFilter())><i class="fa fa-filter"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-header">
              <div class="row w-100">
                <div class="col-auto ml-auto pt-1" @jxnBind($rqChargeCount)>
                   @jxnHtml($rqChargeCount)
                </div>
              </div>
            </div>
            <div class="card-body" @jxnBind($rqChargePage)>
            </div>
          </div>
