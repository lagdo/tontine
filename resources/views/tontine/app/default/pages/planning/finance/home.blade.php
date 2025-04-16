@php
  $rqFund = rq(Ajax\App\Planning\Finance\Fund\Fund::class);
  $rqPool = rq(Ajax\App\Planning\Finance\Pool\Pool::class);
@endphp
          <div class="row sm-screen-selector mt-2 mb-1" id="finance-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="content-pools-home" type="button" class="btn btn-primary">
                  {{ __('tontine.pool.titles.pools') }}
                </button>
                <button data-target="content-funds-home" type="button" class="btn btn-outline-primary">
                  {{ __('tontine.fund.titles.funds') }}
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-pools-home" @jxnBind($rqPool)>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-funds-home" @jxnBind($rqFund)>
            </div>
          </div>
