@php
  $rqSession = rq(Ajax\App\Meeting\Session\Session::class);
  $rqSummary = rq(Ajax\App\Meeting\Summary\Summary::class);

  $rqDeposit = rq(Ajax\App\Meeting\Summary\Pool\Deposit::class);
  $rqRemitment = rq(Ajax\App\Meeting\Summary\Pool\Remitment::class);
  $rqSaving = rq(Ajax\App\Meeting\Summary\Saving\Saving::class);
  $rqClosing = rq(Ajax\App\Meeting\Summary\Saving\Closing::class);
  $rqLoan = rq(Ajax\App\Meeting\Summary\Credit\Loan::class);
  $rqRefund = rq(Ajax\App\Meeting\Summary\Credit\PartialRefund::class);
  $rqRefund = rq(Ajax\App\Meeting\Summary\Credit\Refund::class);
  $rqDisbursement = rq(Ajax\App\Meeting\Summary\Cash\Disbursement::class);
  $rqFixedFee = rq(Ajax\App\Meeting\Summary\Charge\FixedFee::class);
  $rqLibreFee = rq(Ajax\App\Meeting\Summary\Charge\LibreFee::class);
@endphp
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}</h2>
              </div>
              <div class="col">
                <div class="btn-group float-right ml-1" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSession->home())><i class="fa fa-arrow-left"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqSummary->home($session->id))><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row mb-2">
                <div class="col">
                  <ul class="nav nav-pills nav-fill" id="summary-tabs" @jxnOn(['a', 'click'], jq()->tab('show'))>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link active" id="summary-tab-pools" data-target="#summary-pools" role="link" tabindex="0">{!! __('meeting.actions.pools') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="summary-tab-charges" data-target="#summary-charges" role="link" tabindex="0">{!! __('meeting.actions.charges') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="summary-tab-savings" data-target="#summary-savings" role="link" tabindex="0">{!! __('meeting.actions.savings') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="summary-tab-credits" data-target="#summary-credits" role="link" tabindex="0">{!! __('meeting.actions.credits') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="summary-tab-cash" data-target="#summary-cash" role="link" tabindex="0">{!! __('meeting.actions.cash') !!}</a>
                    </li>
                  </nav>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <div class="tab-content" id="summary-tabs-content">

                    <div class="tab-pane fade show active" id="summary-pools" role="tabpanel" aria-labelledby="summary-tab-pools">
                      <div class="row sm-screen-selector mb-3" id="summary-pools-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="content-summary-deposits" type="button" class="btn btn-primary">
                              {!! __('meeting.titles.deposits') !!}
                            </button>
                            <button data-target="content-summary-remitments" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.titles.remitments') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" @jxnBind($rqDeposit) id="content-summary-deposits">
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" @jxnBind($rqRemitment) id="content-summary-remitments">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="summary-charges" role="tabpanel" aria-labelledby="summary-tab-charges">
                      <div class="row sm-screen-selector mb-3" id="summary-charges-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="content-summary-fees-fixed" type="button" class="btn btn-primary">
                              {!! __('meeting.charge.titles.fixed') !!}
                            </button>
                            <button data-target="content-summary-fees-libre" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.charge.titles.variable') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" @jxnBind($rqFixedFee) id="content-summary-fees-fixed">
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" @jxnBind($rqLibreFee) id="content-summary-fees-libre">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="summary-savings" role="tabpanel" aria-labelledby="summary-tab-savings">
                      <div class="row sm-screen-selector mb-3" id="summary-savings-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="content-summary-savings" type="button" class="btn btn-primary">
                              {!! __('meeting.titles.savings') !!}
                            </button>
                            <button data-target="content-summary-closings" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.titles.closings') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" @jxnBind($rqSaving) id="content-summary-savings">
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" @jxnBind($rqClosing) id="content-summary-closings">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="summary-credits" role="tabpanel" aria-labelledby="summary-tab-credits">
                      <div class="row sm-screen-selector mb-3" id="summary-credits-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="content-summary-loans-col" type="button" class="btn btn-primary">
                              {!! __('meeting.titles.loans') !!}
                            </button>
                            <button data-target="content-summary-refunds" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.titles.refunds') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-summary-loans-col">
                          <div @jxnBind($rqLoan) id="content-summary-loans">
                          </div>
                          <div @jxnBind($rqRefund) id="content-summary-partial-refunds">
                          </div>
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" @jxnBind($rqRefund) id="content-summary-refunds">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="summary-cash" role="tabpanel" aria-labelledby="summary-tab-cash">
                      <div class="row">
                        <div class="col-md-12" @jxnBind($rqDisbursement) id="content-summary-disbursements">
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
