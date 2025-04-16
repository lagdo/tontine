@inject('locale', 'Siak\Tontine\Service\LocaleService')
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
                            <th class="currency">{!! __('common.labels.amount') !!}</th>
                            <th class="table-item-menu">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>
@foreach ($loans as $loan)
                          <tr>
                            <td>{{ $loan->member->name }}<br/>{!! $loan->fund->title !!}</td>
                            <td class="currency">
                              {{ $locale->formatMoney($loan->principal, false, true) }}<br/>
                              {{ __('meeting.loan.interest.i' . $loan->interest_type) }}: {{ $loan->fixed_interest ?
                                $locale->formatMoney($loan->interest, false, true) : ($loan->interest_rate / 100) . '%' }}
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
