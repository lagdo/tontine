@php
  $rqOutflow = rq(Ajax\App\Guild\Account\Outflow::class);
  $rqOutflowFunc = rq(Ajax\App\Guild\Account\OutflowFunc::class);
  $rqOutflowPage = rq(Ajax\App\Guild\Account\OutflowPage::class);
@endphp
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col-auto">
                    <h2 class="section-title">{{ __('tontine.account.titles.outflows') }}</h2>
                  </div>
                  <div class="col-auto ml-auto">
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqOutflow->render())><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqOutflowFunc->add())><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body" @jxnBind($rqOutflowPage)>
                </div>
              </div>
