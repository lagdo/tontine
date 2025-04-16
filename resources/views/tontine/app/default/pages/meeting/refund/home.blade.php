@php
  $fundId = pm()->select('refunds-fund-id')->toInt();
  $rqRefund = rq(Ajax\App\Meeting\Session\Credit\Refund\Refund::class);
  $rqRefundFunc = rq(Ajax\App\Meeting\Session\Credit\Refund\RefundFunc::class);
  $rqRefundPage = rq(Ajax\App\Meeting\Session\Credit\Refund\RefundPage::class);
@endphp
                    <div class="row">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
                      </div>
@if($funds->count() > 1)
                      <div class="col-auto" style="padding-left:0;padding-right:0;">
                        <div class="input-group mb-2">
                          {!! $html->select('fund_id', $funds, $fund?->id ?? 0)->id('refunds-fund-id')
                            ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqRefund->fund($fundId))><i class="fa fa-caret-right"></i></button>
                          </div>
                        </div>
                      </div>
@endif
                      <div class="col-auto">
                        <div class="btn-group float-right ml-2 mb-2" role="group">
                          <button type="button" class="btn btn-primary" @jxnClick($rqRefundFunc->toggleFilter())><i class="fa fa-filter"></i></button>
                          <button type="button" class="btn btn-primary" @jxnClick($rqRefund->render())><i class="fa fa-sync"></i></button>
                        </div>
                      </div>
                    </div>
                    <div @jxnBind($rqRefundPage)>
                    </div>
