@php
  $fundId = pm()->select('partial-refunds-fund-id')->toInt();
  $rqPartialRefund = rq(Ajax\App\Meeting\Summary\Refund\Partial\Refund::class);
  $rqPartialRefundPage = rq(Ajax\App\Meeting\Summary\Refund\Partial\RefundPage::class);
@endphp
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.refund.titles.partial') }}</div>
                      </div>
@if($session->opened)
                      <div class="col-auto">
@if($funds->count() > 1)
                        <div class="input-group">
                          {!! $html->select('fund_id', $funds, 0)->id('partial-refunds-fund-id')
                            ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqPartialRefund->fund($fundId))><i class="fa fa-caret-right"></i></button>
                          </div>
                        </div>
@endif
                      </div>
@endif
                    </div>
                    <div @jxnBind($rqPartialRefundPage)>
                    </div>
