@php
  $rqFund = rq(Ajax\App\Guild\Account\Fund::class);
  $rqFundFunc = rq(Ajax\App\Guild\Account\FundFunc::class);
  $rqFundPage = rq(Ajax\App\Guild\Account\FundPage::class);
@endphp
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col">
                    <h2 class="section-title">{!! __('tontine.fund.titles.savings') !!}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqFund->render())><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqFundFunc->add())><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body" @jxnBind($rqFundPage)>
                </div>
              </div>
