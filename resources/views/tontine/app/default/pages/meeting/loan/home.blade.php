@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('fundService', 'Siak\Tontine\Service\Tontine\FundService')
@php
  $loanId = jq()->parent()->attr('data-loan-id')->toInt();
  $rqLoan = rq(Ajax\App\Meeting\Session\Credit\Loan\Loan::class);
  $rqLoanFunc = rq(Ajax\App\Meeting\Session\Credit\Loan\LoanFunc::class);
  $rqBalance = rq(Ajax\App\Meeting\Session\Credit\Loan\Balance::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.loans') }}</div>
                    </div>
                    <div class="col-auto" @jxnBind($rqBalance)>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqLoanFunc->add())><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqLoan->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="content-session-loans" @jxnTarget()>
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
@if (($loan->remitment_id))
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
