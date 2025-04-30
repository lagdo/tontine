@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $loanId = jq()->parent()->attr('data-loan-id')->toInt();
  $rqLoanPage = rq(Ajax\App\Meeting\Summary\Credit\Loan\LoanPage::class);
  $rqBalance = rq(Ajax\App\Meeting\Summary\Credit\Loan\Balance::class);
@endphp
                    <div class="table-responsive" id="content-session-loans-page" @jxnTarget()>
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
                            <td class="table-item-menu">&nbsp;</td>
                          </tr>
@endforeach
                        </tbody>
                      </table>
                    </div> <!-- End table -->
