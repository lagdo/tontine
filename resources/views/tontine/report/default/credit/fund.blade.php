@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{{ __('tontine.report.titles.fund') }} - {!! $fund->title !!}</h5>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <tbody>
@foreach ($fund->loans as $loan)
                        <tr>
                          <th colspan="5">{{ $loan->member->name }}</th>
                        </tr>
                        <tr>
                          <td rowspan="{{ $loan->debts->count() }}">{{ __('meeting.titles.loan') }}</td>
                          <td style="width:30%;" rowspan="{{ $loan->debts->count() }}">{{ $loan->session->title }}</td>
                          <td style="width:20%;" rowspan="{{ $loan->debts->count() }}">{{ $loan->session->date }}</td>
                          <td style="width:15%;">{{ $loan->p_debt->type_label }}</td>
                          <td style="width:15%;text-align:right;">{{ $locale->formatMoney($loan->p_debt->amount, false) }}</td>
                        </tr>
@if ($loan->i_debt !== null)
@php
  $debtAmount = $debtCalculator->getDebtAmount($session, $loan->i_debt);
@endphp
                        <tr>
                          <td>{{ $loan->i_debt->type_label }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($debtAmount, false) }}</td>
                        </tr>
@endif

@php
[$principalRefunds, $interestRefunds] = $loan->all_refunds->partition(fn($refund) => $refund->debt->is_principal);
$rowCount = $loan->all_refunds->count() +
  ($principalRefunds->count() > 0 ? 1 : 0) +
  ($interestRefunds->count() > 0 ? 1 : 0);
@endphp

@foreach ($loan->all_refunds as $refund)
                        <tr>
@if ($loop->first)
                          <td rowspan="{{ $rowCount }}">{{ __('meeting.titles.refund') }}</td>
@endif
                          <td>{{ $refund->session->title }}</td>
                          <td>{{ $refund->session->date }}</td>
                          <td>{{ $refund->debt->type_label }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($refund->amount, false) }}</td>
                        </tr>
@endforeach
@if ($principalRefunds->count() > 0)
                        <tr>
                          <td colspan="2">{{ __('common.labels.total') }}</td>
                          <td >{{ $loan->p_debt->type_label }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($principalRefunds->sum('amount'), false) }}</td>
                        </tr>
@endif
@if ($interestRefunds->count() > 0)
                        <tr>
                          <td colspan="2">{{ __('common.labels.total') }}</td>
                          <td>{{ $loan->i_debt->type_label }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($interestRefunds->sum('amount'), false) }}</td>
                        </tr>
@endif

@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
