@php
  $searchValue = jq('#txt-fee-settlements-search')->val();
  $rqCharge = rq(Ajax\App\Meeting\Summary\Charge\Fixed\Fee::class);
  $rqSettlement = rq(Ajax\App\Meeting\Summary\Charge\Fixed\Settlement::class);
  $rqSettlementPage = rq(Ajax\App\Meeting\Summary\Charge\Fixed\SettlementPage::class);
  $rqTotal = rq(Ajax\App\Meeting\Summary\Charge\Settlement\Total::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">
                        {{ $charge->name }} - {{ __('meeting.titles.settlements') }}
                      </div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqSettlement->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-fee-settlements-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqSettlement->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto ml-auto" @jxnBind($rqTotal, 'fixed') style="padding: 7px 15px 7px 5px;">
                    </div>
                  </div>
                  <div @jxnBind($rqSettlementPage)>
                  </div>
