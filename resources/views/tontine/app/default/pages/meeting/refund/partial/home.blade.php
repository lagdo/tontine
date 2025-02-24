@php
  $fundId = pm()->select('partial-refunds-fund-id')->toInt();
  $rqRefund = rq(Ajax\App\Meeting\Session\Refund\Partial\Refund::class);
  $rqRefundPage = rq(Ajax\App\Meeting\Session\Refund\Partial\RefundPage::class);
  $rqDebt = rq(Ajax\App\Meeting\Session\Refund\Partial\Debt::class);
@endphp
                    <div class="row">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.refund.titles.partial') }}</div>
                      </div>
                      <div class="col-auto" style="padding-left:0;padding-right:0;">
                        <div class="input-group mb-2">
                          {!! $htmlBuilder->select('fund_id', $funds, $fund->id)->id('partial-refunds-fund-id')
                            ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqRefund->fund($fundId))><i class="fa fa-caret-right"></i></button>
                            <button type="button" class="btn btn-primary" @jxnClick($rqDebt->fund($fundId))><i class="fa fa-edit"></i></button>
                          </div>
                        </div>
                      </div>
                      <div class="col-auto">
                        <div class="btn-group float-right ml-2 mb-2" role="group">
                          <button type="button" class="btn btn-primary" @jxnClick($rqRefund->render())><i class="fa fa-sync"></i></button>
                        </div>
                      </div>
                    </div>
                    <div @jxnBind($rqRefundPage)>
                    </div>
