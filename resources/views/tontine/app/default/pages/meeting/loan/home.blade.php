@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('fundService', 'Siak\Tontine\Service\Tontine\FundService')
@php
  $loanId = jq()->parent()->attr('data-loan-id')->toInt();
  $rqLoan = rq(Ajax\App\Meeting\Session\Credit\Loan::class);
  $rqBalance = rq(Ajax\App\Meeting\Session\Credit\Balance::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.loans') }}</div>
                    </div>
                    <div class="col-auto" @jxnBind($rqBalance)>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqLoan->add())><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqLoan->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" @jxnTarget()>
                    <div @jxnEvent(['.btn-loan-edit', 'click'], $rqLoan->edit($loanId))></div>
                    <div @jxnEvent(['.btn-loan-delete', 'click'], $rqLoan->delete($loanId)
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
                          <td>{{ $loan->member->name }}<br/>{!! $fundService->getFundTitle($loan->fund) !!}</td>
                          <td class="currency">
                            {{ $locale->formatMoney($loan->principal, true) }}<br/>
                            {{ __('meeting.loan.interest.i' . $loan->interest_type) }}: {{ $loan->fixed_interest ?
                              $locale->formatMoney($loan->interest, true) : ($loan->interest_rate / 100) . '%' }}
                          </td>
                          <td class="table-item-menu">
@if (($loan->remitment_id))
                            <i class="fa fa-trash-alt"></i>
@else
@include('tontine.app.default.parts.table.menu', [
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
