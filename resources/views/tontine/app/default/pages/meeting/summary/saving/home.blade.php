@php
  $selectFundId = Jaxon\pm()->select('savings-fund-id')->toInt();
  $rqSaving = Jaxon\rq(App\Ajax\Web\Meeting\Summary\Saving\Saving::class);
  $rqSavingPage = Jaxon\rq(App\Ajax\Web\Meeting\Summary\Saving\SavingPage::class);
  $rqSavingCount = Jaxon\rq(App\Ajax\Web\Meeting\Summary\Saving\SavingCount::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col" @jxnShow($rqSavingCount)>
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! $htmlBuilder->select('fund', $funds, $fundId)->id('savings-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqSaving->fund($selectFundId))><i class="fa fa-arrow-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div @jxnShow($rqSavingPage)>
                  </div>
                  <nav @jxnPagination($rqSavingPage)>
                  </nav>
