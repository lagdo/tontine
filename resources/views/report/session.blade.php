              <div class="row">
                <div class="col">
                  <div class="portlet-body form">
                    <form class="form-horizontal" role="form" id="session-agenda">
                      <div class="module-body">
                        <div class="form-group row">
                          <div class="col">
                            {!! __('meeting.titles.agenda') !!}
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col">
                            $session->agenda
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="col">
                  <div class="portlet-body form">
                    <form class="form-horizontal" role="form" id="session-form">
                      <div class="module-body">
                        <div class="form-group row">
                          <div class="col">
                            {!! __('meeting.titles.report') !!}
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col">
                            $session->report
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="meeting-funds">
                </div>
                <div class="col-md-6 col-sm-12" id="meeting-charges">
                </div>
              </div>
@if($tontine->is_financial)
              <div class="row">
                <div class="col-md-7 col-sm-12" id="meeting-biddings">
                </div>
                <div class="col-md-5 col-sm-12" id="meeting-refunds">
                </div>
              </div>
@endif
