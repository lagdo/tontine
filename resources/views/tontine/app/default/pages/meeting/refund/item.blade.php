@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
@php
  $debtId = jq()->parent()->attr('data-debt-id')->toInt();
  $rqRefundFunc = rq(Ajax\App\Meeting\Session\Credit\Refund\RefundFunc::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Credit\Refund\Amount::class);
  $debtAmount = $debtCalculator->getDebtAmount($debt, $session);
  $paidAmount = $debtCalculator->getDebtPaidAmount($debt, $session);
  $dueAmount = $debtCalculator->getDebtDueAmount($debt, $session, false);
  $remainingAmount = $debtCalculator->getDebtDueAmount($debt, $session, true);
  $payableAmount = $debtCalculator->getDebtPayableAmount($debt, $session);
@endphp
                          <td>
                            {{ $debt->loan->member->name }}<br/> {{
                              __('meeting.loan.labels.' . $debt->type) }}: {{
                                $debt->loan->session->title }}
                          </td>
                          <td class="currency">
                            {{ __('meeting.refund.labels.amount', [
                              'amount' => $locale->formatMoney($debtAmount, true),
                            ]) }}<br/>
                            {{ __('meeting.refund.labels.refunded', [
                              'amount' => $locale->formatMoney($paidAmount, true),
                            ]) }}
                          </td>
                          <td class="currency">
                            {{ __('meeting.refund.labels.amount', [
                              'amount' => $locale->formatMoney($dueAmount, true),
                            ]) }}<br/>
                            {{ __('meeting.refund.labels.remaining', [
                              'amount' => $locale->formatMoney($remainingAmount, true),
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
