@php
  $rqOutflow = rq(Ajax\App\Guild\Account\Outflow::class);
  $rqOutflowFunc = rq(Ajax\App\Guild\Account\OutflowFunc::class);
  $rqOutflowPage = rq(Ajax\App\Guild\Account\OutflowPage::class);
@endphp
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.account.titles.outflows') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
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
