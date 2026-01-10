@php
  $fundId = je('refunds-fund-id')->rd()->select()->toInt();
  $rqRefund = rq(Ajax\App\Meeting\Summary\Credit\Refund\Refund::class);
  $rqRefundPage = rq(Ajax\App\Meeting\Summary\Credit\Refund\RefundPage::class);
@endphp
                    <div class="row mb-2">
                      <div class="col-auto">
                        <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
                      </div>
                      <div class="col-auto ml-auto">
@if($funds->count() > 1)
                        <div class="input-group">
                          {!! $html->select('fund_id', $funds, $fund?->id ?? 0)->id('refunds-fund-id')
                            ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqRefund->fund($fundId))><i class="fa fa-caret-right"></i></button>
                          </div>
                        </div>
                      </div>
                      <div class="col-auto">
@endif
                        <div class="btn-group" role="group">
                          <button type="button" class="btn btn-primary" @jxnClick($rqRefund->toggleFilter())><i class="fa fa-filter"></i></button>
                          <button type="button" class="btn btn-primary" @jxnClick($rqRefund->render())><i class="fa fa-sync"></i></button>
                        </div>
                      </div>
                    </div>
                    <div @jxnBind($rqRefundPage)>
                    </div>
