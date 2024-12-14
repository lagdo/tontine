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
                  <button type="button" class="btn btn-primary" @jxnClick($rqSummary->summary($session->id))><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row mb-2">
                <div class="col">
                  <ul class="nav nav-pills nav-fill" id="session-tabs" @jxnTarget()>
                    <div @jxnEvent(['a', 'click'], Jaxon\jq()->tab('show'))></div>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link active" id="session-tab-pools" data-target="#session-pools" role="link" tabindex="0">{!! __('meeting.actions.pools') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-charges" data-target="#session-charges" role="link" tabindex="0">{!! __('meeting.actions.charges') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-savings" data-target="#session-savings" role="link" tabindex="0">{!! __('meeting.actions.savings') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-credits" data-target="#session-credits" role="link" tabindex="0">{!! __('meeting.actions.credits') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-cash" data-target="#session-cash" role="link" tabindex="0">{!! __('meeting.actions.cash') !!}</a>
                    </li>
                  </nav>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <div class="tab-content" id="session-tabs-content">
                    <div class="tab-pane fade show active" id="session-pools" role="tabpanel" aria-labelledby="session-tab-pools">
                      <div class="row sm-screen-selector mb-3" id="session-pools-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="meeting-deposits" type="button" class="btn btn-primary">
                              {!! __('meeting.titles.deposits') !!}
                            </button>
                            <button data-target="meeting-remitments" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.titles.remitments') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" @jxnBind($rqDeposit) id="meeting-deposits">
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" @jxnBind($rqRemitment) id="meeting-remitments">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="session-charges" role="tabpanel" aria-labelledby="session-tab-charges">
                      <div class="row sm-screen-selector mb-3" id="session-charges-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="meeting-fees-fixed" type="button" class="btn btn-primary">
                              {!! __('meeting.charge.titles.fixed') !!}
                            </button>
                            <button data-target="meeting-fees-libre" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.charge.titles.variable') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" @jxnBind($rqFixedFee) id="meeting-fees-fixed">
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" @jxnBind($rqLibreFee) id="meeting-fees-libre">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="session-savings" role="tabpanel" aria-labelledby="session-tab-savings">
                      <div class="row sm-screen-selector mb-3" id="session-savings-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="meeting-savings" type="button" class="btn btn-primary">
                              {!! __('meeting.titles.savings') !!}
                            </button>
                            <button data-target="meeting-closings" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.titles.closings') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" @jxnBind($rqSaving) id="meeting-savings">
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" @jxnBind($rqClosing) id="meeting-closings">
                        </div>
                        <div class="col-12 sm-screen" id="report-fund-savings">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="session-credits" role="tabpanel" aria-labelledby="session-tab-credits">
                      <div class="row sm-screen-selector mb-3" id="session-credits-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="meeting-loans-col" type="button" class="btn btn-primary">
                              {!! __('meeting.titles.loans') !!}
                            </button>
                            <button data-target="meeting-refunds" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.titles.refunds') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="meeting-loans-col">
                          <div @jxnBind($rqLoan) id="meeting-loans">
                          </div>
                          <div @jxnBind($rqRefund) id="meeting-partial-refunds">
                          </div>
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" @jxnBind($rqRefund) id="meeting-refunds">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="session-cash" role="tabpanel" aria-labelledby="session-tab-cash">
                      <div class="row">
                        <div class="col-md-12" @jxnBind($rqDisbursement) id="meeting-disbursements">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
