@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
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
  $debtAmount = $debtCalculator->getDebtAmount($session, $debt);
  $debtDueAmount = $debtCalculator->getDebtDueAmount($session, $debt);
@endphp
                    <tr>
                      <td>
                        {{ $debt->loan->member->name }}<br/>
                        {{ $debt->loan->session->title }}@if ($debt->refund) - {{ $debt->refund->session->title }}@endif
                      </td>
                      <td class="currency">
@if ($debtAmount === $debtDueAmount)
                        {{ $locale->formatMoney($debtAmount, true) }}<br/>
                        {{ __('meeting.loan.labels.' . $debt->type) }}
@else
                        {{ $locale->formatMoney($debtDueAmount, true) }}<br/>
                        {{ __('meeting.loan.labels.' . $debt->type) }}: {{ $locale->formatMoney($debtAmount, true) }}
@endif
                      </td>
                      <td class="table-item-menu" data-debt-id="{{ $debt->id }}">
                        {!! paymentLink($debt->refund, 'refund', $debtAmount === 0 || !$debtCalculator->debtIsEditable($session, $debt)) !!}
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
