@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
@php
  $debtId = Jaxon\jq()->parent()->attr('data-debt-id')->toInt();
  $amount = Jaxon\jq('input', jq()->parent()->parent())->val()->toInt();
  $rqPartialRefund = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\PartialRefund::class);
  $rqPartialRefundEdit = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\PartialRefundEdit::class);
@endphp
                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.titles.partial-refunds') }}</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqPartialRefund->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqPartialRefundEdit->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive" @jxnTarget()>
                    <div @jxnOn(['.btn-partial-refund-edit-amount', 'click', ''], $rqPartialRefund->editAmount($debtId))></div>
                    <div @jxnOn(['.btn-partial-refund-save-amount', 'click', ''], $rqPartialRefund->saveAmount($debtId, $amount))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.refund.labels.loan') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($debts as $debt)
@php
  $payableAmount = $locale->formatMoney($debtCalculator->getDebtPayableAmount($debt, $session), true);
@endphp
                        <tr>
                          <td>
                            {{ $debt->loan->member->name }}: {{ $payableAmount }}<br/> {{
                              __('meeting.loan.labels.' . $debt->type) }}: {{ $debt->loan->session->title }}
                          </td>
                          <td class="currency" id="partial-refund-amount-{{ $debt->id }}" style="width:200px">
@if(!$debt->partial_refund)
@include('tontine.app.default.pages.meeting.refund.partial.amount.edit', [
  'debt' => $debt,
  'amount' => '',
])
@else
@include('tontine.app.default.pages.meeting.refund.partial.amount.show', [
  'debt' => $debt,
  'amount' => $locale->formatMoney($debt->partial_refund->amount, false),
])
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
