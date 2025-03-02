@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
@php
  $debtId = jq()->parent()->attr('data-debt-id')->toInt();
  $amount = jq('input', jq()->parent()->parent())->val()->toInt();
  $rqRefund = rq(Ajax\App\Meeting\Session\Refund\Partial\Refund::class);
  $rqDebt = rq(Ajax\App\Meeting\Session\Refund\Partial\Debt::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Refund\Partial\Amount::class);
  $rqAmountFunc = rq(Ajax\App\Meeting\Session\Refund\Partial\AmountFunc::class);
@endphp
                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.refund.titles.inputs') }}: {!! $fund->title !!}</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqRefund->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqDebt->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive" @jxnTarget()>
                    <div @jxnEvent(['.btn-partial-refund-edit-amount', 'click'], $rqAmountFunc->edit($debtId))></div>
                    <div @jxnEvent(['.btn-partial-refund-save-amount', 'click'], $rqAmountFunc->save($debtId, $amount))></div>

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
  $stash->set('meeting.refund.partial.debt', $debt);
  $payableAmount = $debtCalculator->getDebtPayableAmount($debt, $session);
@endphp
                        <tr>
                          <td>
                            {{ $debt->loan->member->name }}: {{ $locale->formatMoney($payableAmount, true) }}<br/> {{
                              __('meeting.loan.labels.' . $debt->type) }}: {{ $debt->loan->session->title }}
                          </td>
                          <td class="currency amount" @jxnBind($rqAmount, $debt->id)>
                            @if($payableAmount > 0) @jxnHtml($rqAmount) @endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
