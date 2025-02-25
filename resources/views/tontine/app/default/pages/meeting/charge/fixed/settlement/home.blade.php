@php
  $searchValue = jq('#txt-fee-settlements-search')->val();
  $rqCharge = rq(Ajax\App\Meeting\Session\Charge\Fixed\Fee::class);
  $rqSettlementFunc = rq(Ajax\App\Meeting\Session\Charge\Fixed\SettlementFunc::class);
  $rqSettlementPage = rq(Ajax\App\Meeting\Session\Charge\Fixed\SettlementPage::class);
  $rqTotal = rq(Ajax\App\Meeting\Session\Charge\Settlement\Total::class);
  $rqAction = rq(Ajax\App\Meeting\Session\Charge\Settlement\Action::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">
                        {{ $charge->name }} - {{ __('meeting.titles.settlements') }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqSettlementFunc->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-fee-settlements-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqSettlementFunc->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto" @jxnBind($rqTotal, 'fixed') style="padding: 7px 15px 7px 5px;">
                    </div>
                    <div class="col-auto" @jxnBind($rqAction, 'fixed') style="padding: 7px 15px 7px 5px;">
                    </div>
                  </div>
                  <div @jxnBind($rqSettlementPage)>
                  </div>
