@php
  $rqCharge = rq(Ajax\App\Tontine\Options\Charge::class);
  $rqChargeFunc = rq(Ajax\App\Tontine\Options\ChargeFunc::class);
  $rqChargePage = rq(Ajax\App\Tontine\Options\ChargePage::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col-auto">
                <h2 class="section-title">{{ __('tontine.charge.titles.charges') }}</h2>
              </div>
              <div class="col">
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
