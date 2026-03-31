                  <div class="table-title">
                    {{ __('tontine.report.titles.fund') }} - {!! $fund->title !!}
                  </div>
                  <div class="table">
                    <table>
                      <tbody>
@foreach ($fund->loans as $loans)
                        <tr class="member">
                          <th colspan="5">{{ $loans[0]->member->name }}</th>
                        </tr>
@foreach ($loans as $loan)
@php
  $rowspan = $loan->debts->count() + (!$loan->no_deadline ? 1 : 0);
@endphp
                        <tr>
                          <td rowspan="{{ $rowspan }}">{{ __('meeting.titles.loan') }}</td>
                          <td style="width:30%;" colspan="2" rowspan="{{ $rowspan }}">{{ $loan->session->title }}</td>
                          <td style="width:20%;" rowspan="{{ $rowspan }}">{{ $loan->session->date('day_date') }}</td>
                          <td style="width:15%;">{{ __('meeting.report.labels.' . $loan->p_debt->type) }}</td>
                          <td style="width:15%;text-align:right;">{{ $locale->formatMoney($loan->p_debt->amount, false) }}</td>
                        </tr>
@if ($loan->i_debt !== null)
                        <tr>
                          <td>{{ __('meeting.report.labels.' . $loan->i_debt->type) }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($loan->iDebtAmount, false) }}</td>
                        </tr>
@endif
@if (!$loan->no_deadline)
                        <tr style="border-top: none;">
                          <td style="width:30%;" colspan="2">
                            {{ __('meeting.loan.labels.deadline', [
                              'deadline' => $loan->deadline_session !== null ?
                                $loan->deadline_session->title : $loan->date('deadline_date'),
                            ]) }}@if ($loan->deadline_exceeded) ({{ __('meeting.loan.labels.exceeded') }})@endif
                          </td>
                        </tr>
@endif

@php
[$principalRefunds, $interestRefunds] = $loan->all_refunds->partition(fn($refund) => $refund->debt->is_principal);
$principalRefundAmount = 0;
$principalDueAmount = $loan->p_debt->amount;
$interestRefundAmount = 0;
$interestDueAmount = $loan->iDebtAmount;
if($principalRefunds->count() > 0)
{
  $principalRefundAmount = $principalRefunds->sum('amount');
  $principalDueAmount -= $principalRefundAmount;
}
if($interestRefunds->count() > 0)
{
  $interestRefundAmount = $interestRefunds->sum('amount');
  $interestDueAmount -= $interestRefundAmount;
}

$refundCount = $loan->all_refunds->count();
// Two rows are added for the due amounts.
$rowCount = $refundCount > 0 ? $refundCount + 2 : 0;
@endphp

@foreach ($loan->all_refunds as $refund)
                        <tr>
@if ($loop->first)
                          <td rowspan="{{ $rowCount }}">{{ __('meeting.titles.refund') }}</td>
@endif
                          <td colspan="2">{{ $refund->session->title }}</td>
                          <td>{{ $refund->session->date('day_date') }}</td>
                          <td>{{ __('meeting.report.labels.' . $refund->debt->type) }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($refund->amount, false) }}</td>
                        </tr>
@endforeach
@if ($refundCount > 0)
                        <tr>
                          <td>{{ __('meeting.report.labels.' . $loan->p_debt->type) }}</td>
                          <td>{{ __('meeting.report.labels.due') }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($principalDueAmount, false) }}</td>
                          <td >{{ __('meeting.report.labels.paid') }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($principalRefundAmount, false) }}</td>
                        </tr>
                        <tr>
                          <td>{{ __('meeting.report.labels.' . $loan->i_debt->type) }}</td>
                          <td>{{ __('meeting.report.labels.due') }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($interestDueAmount, false) }}</td>
                          <td>{{ __('meeting.report.labels.paid') }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($interestRefundAmount, false) }}</td>
                        </tr>
@endif

@endforeach
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
