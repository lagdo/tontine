@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
@inject('paymentService', 'Siak\Tontine\Service\Meeting\PaymentServiceInterface')
@php
  $refundId = jq()->parent()->attr('data-partial-refund-id')->toInt();
  $rqRefund = rq(Ajax\App\Meeting\Session\Credit\Partial\Refund::class);
  $rqRefundPage = rq(Ajax\App\Meeting\Session\Credit\Partial\RefundPage::class);
@endphp
                    <div class="table-responsive" id="content-session-partial-refunds-page" @jxnTarget()>
                      <div @jxnEvent(['.btn-partial-refund-edit', 'click'], $rqRefund->edit($refundId))></div>
                      <div @jxnEvent(['.btn-partial-refund-delete', 'click'], $rqRefund->delete($refundId)
                        ->confirm(__('meeting.refund.questions.delete')))></div>

                      <table class="table table-bordered responsive">
                        <thead>
                          <tr>
                            <th>{!! __('meeting.refund.labels.loan') !!}</th>
                            <th class="currency">{!! __('common.labels.amount') !!}</th>
                            <th class="table-item-menu">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>
@foreach($refunds as $refund)
@php
  $dueAmount = $debtCalculator->getDebtDueAmount($refund->debt, $session, false);
@endphp
                          <tr>
                            <td>
                              {{ $refund->debt->loan->member->name }}<br/> {{
                                __('meeting.loan.labels.' . $refund->debt->type) }}: {{ $refund->debt->loan->session->title }}
                            </td>
                            <td class="currency">
                              {{ $locale->formatMoney($refund->amount, true) }}<br/>
                              {{ $locale->formatMoney($dueAmount, true) }}
                            </td>
                            <td class="table-item-menu" data-partial-refund-id="{{ $refund->id }}">
@if( $refund->debt->refund !== null || !$paymentService->isEditable($refund) )
                              {!! paymentLink($refund, 'partial-refund', $refund->debt->refund !== null || !$session->opened) !!}
@else
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-partial-refund-id',
  'dataIdValue' => $refund->id,
  'menus' => [[
    'class' => 'btn-partial-refund-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-partial-refund-delete',
    'text' => __('common.actions.delete'),
  ]],
])
@endif
                            </td>
                          </tr>
@endforeach
                        </tbody>
                      </table>
                      <nav @jxnPagination($rqRefundPage)>
                      </nav>
                    </div> <!-- End table -->
