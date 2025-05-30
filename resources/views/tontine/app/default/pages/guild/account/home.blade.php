@php
  $rqFund = rq(Ajax\App\Guild\Account\Fund::class);
  $rqOutflow = rq(Ajax\App\Guild\Account\Outflow::class);
@endphp
          <div class="row sm-screen-selector mt-2 mb-1" id="account-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="content-funds-home" type="button" class="btn btn-primary">
                  {{ __('tontine.fund.titles.funds') }}
                </button>
                <button data-target="content-outflows-home" type="button" class="btn btn-outline-primary">
                  {{ __('tontine.account.titles.outflows') }}
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-funds-home" @jxnBind($rqFund)>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-outflows-home" @jxnBind($rqOutflow)>
            </div>
          </div>
