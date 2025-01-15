@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqPool = rq(Ajax\App\Planning\Subscription\Pool::class);
  $rqPoolSection = rq(Ajax\App\Planning\Subscription\PoolSection::class);
@endphp
          <div class="row" id="subscription-sm-screens">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-subscription-pools" @jxnBind($rqPool)>
              @jxnHtml($rqPool)
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-subscription-members" @jxnBind($rqPoolSection)>
            </div>
          </div>
