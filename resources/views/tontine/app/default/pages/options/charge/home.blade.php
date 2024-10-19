@php
  $rqCharge = Jaxon\rq(App\Ajax\Web\Tontine\Charge::class);
  $rqChargePage = Jaxon\rq(App\Ajax\Web\Tontine\ChargePage::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col-auto">
                <h2 class="section-title">{{ __('tontine.charge.titles.charges') }}</h2>
              </div>
              <div class="col">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqCharge->select())><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body">
              <div @jxnShow($rqChargePage)>
              </div>
              <nav @jxnPagination($rqChargePage)>
              </nav>
            </div>
          </div>
