@php
  $searchValue = Jaxon\jq('#txt-fee-settlements-search')->val();
  $rqCharge = Jaxon\rq(Ajax\App\Meeting\Session\Charge\FixedFee::class);
  $rqSettlement = Jaxon\rq(Ajax\App\Meeting\Session\Charge\Fixed\Settlement::class);
  $rqSettlementPage = Jaxon\rq(Ajax\App\Meeting\Session\Charge\Fixed\SettlementPage::class);
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
                        <button type="button" class="btn btn-primary" @jxnClick($rqSettlement->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col">
                      <div class="input-group">
                        {!! $htmlBuilder->text('search', '')->id('txt-fee-settlements-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqSettlement->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto" id="meeting-settlements-total">
                    </div>
                    <div class="col-auto" id="meeting-settlements-action">
                    </div>
                  </div>
                  <div @jxnShow($rqSettlementPage)>
                  </div>
