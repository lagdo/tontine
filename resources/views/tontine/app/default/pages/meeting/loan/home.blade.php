@php
  $rqLoan = rq(Ajax\App\Meeting\Session\Credit\Loan\Loan::class);
  $rqLoanPage = rq(Ajax\App\Meeting\Session\Credit\Loan\LoanPage::class);
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
                  <div @jxnBind($rqLoanPage)>
                  </div>
