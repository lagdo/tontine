@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
@php
  $debtId = jq()->parent()->attr('data-debt-id')->toInt();
  $rqRefundFunc = rq(Ajax\App\Meeting\Session\Credit\Refund\RefundFunc::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Credit\Refund\Amount::class);
@endphp
                          <td>
                            {!! $debt->member !!}<br/>
                            {!! $debt->loan->session->title !!}
                          </td>
                          <td>
                            {!! __('meeting.refund.labels.loan', [
                              'member' => __('meeting.loan.labels.' . $debt->type),
                              'amount' => $locale->formatMoney($debtAmount, false, true),
                            ]) !!}<br/>
                            {!! $debt->loan->fund->title !!}
                          </td>
                          <td class="currency">
                            {{ __('meeting.refund.labels.before', [
                              'amount' => $locale->formatMoney($paidAmount, false, true),
                            ]) }}<br/>
                            {{ __('meeting.refund.labels.total', [
                              'amount' => $locale->formatMoney($totalPaidAmount, false, true),
                            ]) }}
                          </td>
                          <td class="currency">
                            {{ __('meeting.refund.labels.before', [
                              'amount' => $locale->formatMoney($amountDueBeforeSession, false, true),
                            ]) }}<br/>
                            {{ __('meeting.refund.labels.after', [
                              'amount' => $locale->formatMoney($amountDueAfterSession, false, true),
                            ]) }}
                          </td>
                          <td class="currency amount" @jxnBind($rqAmount, $debt->id)>
                            @if($debt->canPartiallyRefund && $payableAmount > 0) @jxnHtml($rqAmount) @endif
                          </td>
                          <td class="table-item-menu">
                            <div data-debt-id="{{ $debt->id }}" @jxnTarget()>
                              <div @jxnEvent(['.btn-add-refund', 'click'], $rqRefundFunc->create($debtId))></div>
                              <div @jxnEvent(['.btn-del-refund', 'click'], $rqRefundFunc->delete($debtId))></div>

                              {!! paymentLink($debt->refund, 'refund', !$debt->isEditable) !!}
                            </div>
                          </td>
