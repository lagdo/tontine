@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('paymentService', 'Siak\Tontine\Service\Meeting\PaymentServiceInterface')
@php
  $receivableId = Jaxon\jq()->parent()->attr('data-receivable-id')->toInt();
  $amount = Jaxon\jq('input', Jaxon\jq()->parent()->parent())->val()->toInt();
  $rqReceivable = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\Receivable::class);
  $rqReceivablePage = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivablePage::class);
@endphp
                  <div class="table-responsive" id="meeting-pool-deposits" @jxnTarget()>
                    <div @jxnEvent(['.btn-add-deposit', 'click'], $rqReceivable->addDeposit($receivableId))></div>
                    <div @jxnEvent(['.btn-del-deposit', 'click'], $rqReceivable->delDeposit($receivableId))></div>
                    <div @jxnEvent(['.btn-save-deposit', 'click'], $rqReceivable->saveAmount($receivableId, $amount))></div>
                    <div @jxnEvent(['.btn-edit-deposit', 'click'], $rqReceivable->editAmount($receivableId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
@if ($pool->deposit_fixed)
                          <th class="table-item-menu">&nbsp;</th>
@endif
                        </tr>
                      </thead>
                      <tbody>
@foreach ($receivables as $receivable)
                        <tr>
                          <td>{{ $receivable->member }}</td>
@if ($pool->deposit_fixed)
                          <td class="currency">{{ $locale->formatMoney($receivable->amount, true) }}</td>
                          <td class="table-item-menu" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}">
                            {!! paymentLink($receivable->deposit, 'deposit', !$session->opened) !!}
                          </td>
@else
                          <td class="currency" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}" style="width:200px">
@if ($session->closed)
                            @include('tontine.app.default.pages.meeting.deposit.libre.closed', [
                              'amount' => !$receivable->deposit ? '' : $locale->formatMoney($receivable->deposit->amount, true),
                            ])
@elseif (!$receivable->deposit)
                            @include('tontine.app.default.pages.meeting.deposit.libre.edit', [
                              'id' => $receivable->id,
                              'amount' => '',
                            ])
@else
                            @include('tontine.app.default.pages.meeting.deposit.libre.show', [
                              'id' => $receivable->id,
                              'amount' => $locale->formatMoney($receivable->deposit->amount, false),
                              'editable' => $paymentService->isEditable($receivable->deposit),
                            ])
@endif
                          </td>
@endif
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqReceivablePage)>
                    </nav>
                  </div> <!-- End table -->
