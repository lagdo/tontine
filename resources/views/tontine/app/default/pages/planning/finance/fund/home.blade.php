@php
  $rqFund = rq(Ajax\App\Planning\Finance\Fund\Fund::class);
  $rqFundPage = rq(Ajax\App\Planning\Finance\Fund\FundPage::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.fund.titles.funds') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqFund->render())><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqFundPage)>
            </div>
          </div>
