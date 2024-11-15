@php
  $rqFund = Jaxon\rq(Ajax\App\Tontine\Options\Fund::class);
  $rqCategory = Jaxon\rq(Ajax\App\Tontine\Options\Category::class);
  $rqCharge = Jaxon\rq(Ajax\App\Tontine\Options\Charge::class);
@endphp
          <div class="row sm-screen-selector mb-3" id="options-sm-screens">
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
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" @jxnShow($rqFund)>
              @jxnHtml($rqFund)
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" @jxnShow($rqCategory)>
              @jxnHtml($rqCategory)
            </div>
          </div>
          <div class="row">
            <div class="col-12 sm-screen" @jxnShow($rqCharge)>
              @jxnHtml($rqCharge)
            </div>
          </div>
