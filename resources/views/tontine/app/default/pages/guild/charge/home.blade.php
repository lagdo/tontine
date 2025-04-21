@php
  $rqCharge = rq(Ajax\App\Guild\Charge\Charge::class);
  $rqChargeFunc = rq(Ajax\App\Guild\Charge\ChargeFunc::class);
  $rqChargePage = rq(Ajax\App\Guild\Charge\ChargePage::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.charge.titles.charges') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqChargeFunc->select())><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqChargePage)>
            </div>
          </div>
