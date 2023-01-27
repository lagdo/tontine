                  <div class="portlet-body form">
                    <form class="form-horizontal" role="form" id="session-agenda">
                      <div class="module-body">
                        <div class="form-group row">
                          <div class="col-sm-6">
                            {!! __('meeting.titles.agenda') !!}
                          </div>
                          <div class="col-sm-6">
                            <div class="btn-group float-right" role="group" aria-label="">
                              <button type="button" class="btn btn-primary" id="btn-save-agenda"><i class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col-sm-12">
                            {!! Form::textarea('agenda', $session->agenda, ['class' => 'form-control', 'id' => 'text-session-agenda']) !!}
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
