@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
@php
  $debtId = jq()->parent()->attr('data-debt-id')->toInt();
  $rqRefundFunc = rq(Ajax\App\Meeting\Session\Refund\Total\RefundFunc::class);
  $rqRefundPage = rq(Ajax\App\Meeting\Session\Refund\Total\RefundPage::class);
@endphp
                    <div class="table-responsive" id="content-session-refunds-page" @jxnTarget()>
                      <div @jxnEvent(['.btn-add-refund', 'click'], $rqRefundFunc->create($debtId))></div>
                      <div @jxnEvent(['.btn-del-refund', 'click'], $rqRefundFunc->delete($debtId))></div>

                      <table class="table table-bordered responsive">
                        <thead>
                          <tr>
                            <th>{!! __('meeting.labels.member') !!}</th>
                            <th class="currency">{!! __('common.labels.amount') !!}</th>
                            <th class="table-item-menu">{!! __('common.labels.paid') !!}</th>
                          </tr>
                        </thead>
                        <tbody>
@foreach($debts as $debt)
@php
  $debtAmount = $debtCalculator->getDebtAmount($debt, $session);
  $debtDueAmount = $debtCalculator->getDebtDueAmount($debt, $session, true);
@endphp
                          <tr>
                            <td>
                              {{ $debt->loan->member->name }}<br/> {{
                                __('meeting.loan.labels.' . $debt->type) }}: {{ $debt->loan->session->title }}
                            </td>
                            <td class="currency">
                              {{ __('meeting.report.labels.due') }} {{ $locale->formatMoney($debtDueAmount, true) }}<br/>
                              {{ $locale->formatMoney($debtAmount, true) }}
                            </td>
                            <td class="table-item-menu" data-debt-id="{{ $debt->id }}">
                              {!! paymentLink($debt->refund, 'refund', !$debt->isEditable) !!}
                            </td>
                          </tr>
@endforeach
                        </tbody>
                      </table>
                      <nav @jxnPagination($rqRefundPage)>
                      </nav>
                    </div> <!-- End table -->
