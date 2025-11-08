@php
  $debtId = jq()->parent()->attr('data-debt-id')->toInt();
  $rqRefundFunc = rq(Ajax\App\Meeting\Session\Credit\Refund\RefundFunc::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Credit\Refund\Amount::class);
@endphp
                          <td>
                            <div>{!! $debt->member !!}</div>
                            <div>{!! $debt->loan->session->title !!}</div>
                          </td>
                          <td>
                            <div>{!! __('meeting.refund.labels.loan', [
                              'member' => __('meeting.loan.labels.' . $debt->type),
                              'amount' => $locale->formatMoney($debtAmount, false, true),
                            ]) !!}</div>
                            <div>{!! $debt->loan->fund->title !!}</div>
                          </td>
                          <td class="currency">
                            <div>{{ __('meeting.refund.labels.before', [
                              'amount' => $locale->formatMoney($paidAmount, false, true),
                            ]) }}</div>
                            <div>{{ __('meeting.refund.labels.total', [
                              'amount' => $locale->formatMoney($totalPaidAmount, false, true),
                            ]) }}</div>
                          </td>
                          <td class="currency">
                            <div>{{ __('meeting.refund.labels.before', [
                              'amount' => $locale->formatMoney($amountDueBeforeSession, false, true),
                            ]) }}</div>
                            <div>{{ __('meeting.refund.labels.after', [
                              'amount' => $locale->formatMoney($amountDueAfterSession, false, true),
                            ]) }}</div>
                          </td>
                          <td class="currency amount" @jxnBind($rqAmount, $debt->id)>
                            @if($debt->canPartiallyRefund && $payableAmount > 0) @jxnHtml($rqAmount) @endif
                          </td>
                          <td class="table-item-menu">
                            <div data-debt-id="{{ $debt->id }}" @jxnEvent([
                              ['.btn-add-refund', 'click', $rqRefundFunc->create($debtId)],
                              ['.btn-del-refund', 'click', $rqRefundFunc->delete($debtId)],
                            ])>
                              {!! paymentLink($debt->refund, 'refund', !$debt->isEditable) !!}
                            </div>
                          </td>
