          <div class="section-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}</h2>
              </div>
              <div class="col">
@include('tontine.app.default.pages.report.session.exports', ['sessionId' => $session->id])
@include('tontine.app.default.pages.meeting.session.action')
@include('tontine.app.default.pages.meeting.session.open', ['session' => $session])
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row mb-2">
                <div class="col">
                  <ul class="nav nav-pills nav-fill" id="session-tabs">
                    <li class="nav-item" role="presentation">
                      <a class="nav-link active" id="session-tab-pools" data-target="#session-pools" href="javascript:void(0)">{!! __('meeting.actions.pools') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-charges" data-target="#session-charges" href="javascript:void(0)">{!! __('meeting.actions.charges') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-savings" data-target="#session-savings" href="javascript:void(0)">{!! __('meeting.actions.savings') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-credits" data-target="#session-credits" href="javascript:void(0)">{!! __('meeting.actions.credits') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-cash" data-target="#session-cash" href="javascript:void(0)">{!! __('meeting.actions.cash') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-reports" data-target="#session-reports" href="javascript:void(0)">{!! __('meeting.actions.reports') !!}</a>
                    </li>
                  </nav>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <div class="tab-content" id="session-tabs-content">
                    <div class="tab-pane fade show active" id="session-pools" role="tabpanel" aria-labelledby="session-tab-pools">
                      <div class="row">
                        <div class="col-md-6 col-sm-12" id="meeting-deposits">
                        </div>
                        <div class="col-md-6 col-sm-12" id="meeting-remitments">
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="session-charges" role="tabpanel" aria-labelledby="session-tab-charges">
                      <div class="row">
                        <div class="col-md-6 col-sm-12" id="meeting-fees-fixed">
                        </div>
                        <div class="col-md-6 col-sm-12" id="meeting-fees-libre">
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="session-savings" role="tabpanel" aria-labelledby="session-tab-savings">
                      <div class="row">
                        <div class="col-md-6 col-sm-12" id="meeting-savings">
                        </div>
                        <div class="col-md-6 col-sm-12" id="meeting-closings">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12" id="report-fund-savings">
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="session-credits" role="tabpanel" aria-labelledby="session-tab-credits">
                      <div class="row">
                        <div class="col-md-6 col-sm-12">
                          <div id="meeting-loans">
                          </div>
                          <div id="meeting-partial-refunds">
                          </div>
                        </div>
                        <div class="col-md-6 col-sm-12" id="meeting-refunds">
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="session-cash" role="tabpanel" aria-labelledby="session-tab-cash">
                      <div class="row">
                        <div class="col-md-12" id="meeting-disbursements">
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="session-reports" role="tabpanel" aria-labelledby="session-tab-reports">
                      <div class="row">
                        <div class="col-md-6 col-sm-12">
@include('tontine.app.default.pages.meeting.session.agenda', ['session' => $session])
                        </div>
                        <div class="col-md-6 col-sm-12">
@include('tontine.app.default.pages.meeting.session.report', ['session' => $session])
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
