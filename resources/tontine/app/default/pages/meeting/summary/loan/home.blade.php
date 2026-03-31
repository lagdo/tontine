@php
  $rqLoan = rq(Ajax\App\Meeting\Summary\Credit\Loan\Loan::class);
  $rqLoanPage = rq(Ajax\App\Meeting\Summary\Credit\Loan\LoanPage::class);
  $rqBalance = rq(Ajax\App\Meeting\Summary\Credit\Loan\Balance::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.titles.loans') }}</div>
                    </div>
                    <div class="col-auto ml-auto" @jxnBind($rqBalance)>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqLoan->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div @jxnBind($rqLoanPage)>
                  </div>
