@php
  $rqRefundPage = rq(Ajax\App\Meeting\Summary\Credit\Refund\RefundPage::class);
  $rqRefundItem = rq(Ajax\App\Meeting\Summary\Credit\Refund\RefundItem::class);
@endphp
                    <div class="table-responsive" id="content-session-refunds-page">
                      <table class="table table-bordered responsive">
                        <thead>
                          <tr>
                            <th>{!! __('meeting.labels.member') !!}</th>
                            <th>{!! __('meeting.refund.titles.loan') !!}</th>
                            <th class="currency">{!! __('meeting.refund.titles.paid') !!}</th>
                            <th class="currency">{!! __('meeting.refund.titles.debt') !!}</th>
                            <th class="currency">{!! __('meeting.refund.titles.partial') !!}</th>
                            <th class="table-item-menu">{!! __('meeting.refund.titles.final') !!}</th>
                          </tr>
                        </thead>
                        <tbody>
@foreach($debts as $debt)
@php
  $stash->set('summary.refund.debt', $debt);
@endphp
                          <tr @jxnBind($rqRefundItem, $debt->id)>
                            @jxnHtml($rqRefundItem)
                          </tr>
@endforeach
                        </tbody>
                      </table>
                      <nav @jxnPagination($rqRefundPage)>
                      </nav>
                    </div> <!-- End table -->
