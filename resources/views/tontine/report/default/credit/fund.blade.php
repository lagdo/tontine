@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{{ __('tontine.report.titles.fund') }} - {!! $fund->title !!}</h5>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <tbody>
@foreach ($fund->loans as $loans)
                        <tr>
                          <th colspan="5">{{ $loans[0]->member->name }}</th>
                        </tr>
@foreach ($loans as $loan)
                        <tr>
                          <td rowspan="{{ $loan->debts->count() }}">{{ __('meeting.titles.loan') }}</td>
                          <td style="width:30%;" colspan="2" rowspan="{{ $loan->debts->count() }}">{{ $loan->session->title }}</td>
                          <td style="width:20%;" rowspan="{{ $loan->debts->count() }}">{{ $loan->session->date('day_date') }}</td>
                          <td style="width:15%;">{{ __('meeting.report.labels.' . $loan->p_debt->type) }}</td>
                          <td style="width:15%;text-align:right;">{{ $locale->formatMoney($loan->p_debt->amount, false) }}</td>
                        </tr>
@if ($loan->i_debt !== null)
                        <tr>
                          <td>{{ __('meeting.report.labels.' . $loan->i_debt->type) }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($loan->iDebtAmount, false) }}</td>
                        </tr>
@endif

@php
[$principalRefunds, $interestRefunds] = $loan->all_refunds->partition(fn($refund) => $refund->debt->is_principal);
$rowCount = $loan->all_refunds->count();
$principalRefundAmount = 0;
$principalDueAmount = 0;
$interestRefundAmount = 0;
$interestDueAmount = 0;
if($principalRefunds->count() > 0)
{
  $rowCount++;
  $principalRefundAmount = $principalRefunds->sum('amount');
  $principalDueAmount = $loan->p_debt->amount - $principalRefundAmount;
}
if($interestRefunds->count() > 0)
{
  $rowCount++;
  $interestRefundAmount = $interestRefunds->sum('amount');
  $interestDueAmount = $loan->iDebtAmount - $interestRefundAmount;
}
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
@if ($principalRefunds->count() > 0)
                        <tr>
                          <td>{{ __('meeting.report.labels.' . $loan->p_debt->type) }}</td>
                          <td>{{ __('meeting.report.labels.due') }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($principalDueAmount, false) }}</td>
                          <td >{{ __('meeting.report.labels.paid') }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($principalRefundAmount, false) }}</td>
                        </tr>
@endif
@if ($interestRefunds->count() > 0)
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
