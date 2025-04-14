@php
  $rqDisbursement = rq(Ajax\App\Guild\Account\Disbursement::class);
  $rqDisbursementFunc = rq(Ajax\App\Guild\Account\DisbursementFunc::class);
  $rqDisbursementPage = rq(Ajax\App\Guild\Account\DisbursementPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.account.titles.disbursements') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqDisbursement->render())><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqDisbursementFunc->add())><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body" @jxnBind($rqDisbursementPage)>
                </div>
              </div>
