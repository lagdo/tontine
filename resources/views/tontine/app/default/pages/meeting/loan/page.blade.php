@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('fundService', 'Siak\Tontine\Service\Tontine\FundService')
@php
  $loanId = jq()->parent()->attr('data-loan-id')->toInt();
  $rqLoanPage = rq(Ajax\App\Meeting\Session\Credit\Loan\LoanPage::class);
  $rqLoanFunc = rq(Ajax\App\Meeting\Session\Credit\Loan\LoanFunc::class);
  $rqBalance = rq(Ajax\App\Meeting\Session\Credit\Loan\Balance::class);
@endphp
                    <div class="table-responsive" id="content-session-loans-page" @jxnTarget()>
                      <div @jxnEvent(['.btn-loan-edit', 'click'], $rqLoanFunc->edit($loanId))></div>
                      <div @jxnEvent(['.btn-loan-delete', 'click'], $rqLoanFunc->delete($loanId)
                        ->confirm(__('meeting.loan.questions.delete')))></div>

                      <table class="table table-bordered responsive">
                        <thead>
                          <tr>
                            <th>{!! __('meeting.labels.member') !!}</th>
                            <th>{!! __('meeting.loan.labels.fund') !!}</th>
                            <th class="currency">{!! __('common.labels.amount') !!}</th>
                            <th>{!! __('meeting.loan.labels.interest') !!}</th>
                            <th class="table-item-menu">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>
@foreach ($loans as $loan)
                          <tr>
                            <td>{{ $loan->member->name }}</td>
                            <td>{!! $fundService->getFundTitle($loan->fund) !!}</td>
                            <td class="currency">{{ $locale->formatMoney($loan->principal, true) }}</td>
                            <td>
                              {{ __('meeting.loan.interest.i' . $loan->interest_type) }}: {{ $loan->fixed_interest ?
                                $locale->formatMoney($loan->interest, true) : ($loan->interest_rate / 100) . '%' }}
                            </td>
                            <td class="table-item-menu">
@if ($loan->remitment_id > 0)
                              <i class="fa fa-trash-alt"></i>
@else
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-loan-id',
  'dataIdValue' => $loan->id,
  'menus' => [[
    'class' => 'btn-loan-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-loan-delete',
    'text' => __('common.actions.delete'),
  ]],
])
@endif
                            </td>
                          </tr>
@endforeach
                        </tbody>
                      </table>
                    </div> <!-- End table -->
