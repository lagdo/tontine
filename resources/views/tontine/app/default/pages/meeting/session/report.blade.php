                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.report') !!}</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-save-report"><i class="fa fa-save"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div id="session-report">
                        {!! $session->report !!}
                      </div>
                    </div>
                  </div>
