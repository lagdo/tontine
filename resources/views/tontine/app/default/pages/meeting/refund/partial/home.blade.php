@php
  $fundId = Jaxon\pm()->select('partial-refunds-fund-id')->toInt();
  $rqRefund = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\Partial\Refund::class);
  $rqRefundPage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\Partial\RefundPage::class);
  $rqAmount = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\Partial\Amount::class);
@endphp
                    <div class="row">
                      <div class="col-auto">
                        <div class="section-title mt-0">{{ __('meeting.titles.partial-refunds') }}</div>
                      </div>
                      <div class="col">
@if($funds->count() > 1)
                        <div class="input-group mb-2">
                          {!! $htmlBuilder->select('fund_id', $funds, $currentFundId)->id('partial-refunds-fund-id')
                            ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqRefund->fund($fundId))><i class="fa fa-arrow-right"></i></button>
                            <button type="button" class="btn btn-primary" @jxnClick($rqAmount->fund($fundId))><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-primary" @jxnClick($rqRefund->render())><i class="fa fa-sync"></i></button>
                          </div>
                        </div>
@else
                        <div class="btn-group float-right ml-2 mb-2" role="group">
                          <button type="button" class="btn btn-primary" @jxnClick($rqAmount->fund($fundId))><i class="fa fa-edit"></i></button>
                          <button type="button" class="btn btn-primary" @jxnClick($rqRefund->render())><i class="fa fa-sync"></i></button>
                        </div>
@endif
                      </div>
                    </div>
                    <div @jxnShow($rqRefundPage)>
                    </div>
