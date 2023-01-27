                  <div class="portlet-body form">
                    <form class="form-horizontal" role="form" id="session-form">
                      <div class="module-body">
                        <div class="form-group row">
                          <div class="col-sm-6">
                            {!! __('meeting.titles.report') !!}
                          </div>
                          <div class="col-sm-6">
                            <div class="btn-group float-right" role="group" aria-label="">
                              <button type="button" class="btn btn-primary" id="btn-save-report"><i class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col-sm-12">
                            {!! Form::textarea('report', $session->report, ['class' => 'form-control', 'id' => 'text-session-report']) !!}
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
