@php
  $rqFund = rq(Ajax\App\Tontine\Options\Fund::class);
  $rqCategory = rq(Ajax\App\Tontine\Options\Category::class);
  $rqCharge = rq(Ajax\App\Tontine\Options\Charge::class);
@endphp
          <div class="row sm-screen-selector mt-2 mb-1" id="options-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="content-funds-home" type="button" class="btn btn-primary">
                  {{ __('tontine.fund.titles.funds') }}
                </button>
                <button data-target="content-categories-home" type="button" class="btn btn-outline-primary">
                  {{ __('tontine.category.titles.categories') }}
                </button>
                <button data-target="content-charges-home" type="button" class="btn btn-outline-primary">
                  {{ __('tontine.charge.titles.charges') }}
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-funds-home" @jxnBind($rqFund)>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-categories-home" @jxnBind($rqCategory)>
            </div>
          </div>
          <div class="row">
            <div class="col-12 sm-screen" id="content-charges-home" @jxnBind($rqCharge)>
            </div>
          </div>
