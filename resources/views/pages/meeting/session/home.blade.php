          <div class="section-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}</h2>
              </div>
              <div class="col">
                <div class="btn-group float-right ml-2" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-session-back"><i class="fa fa-arrow-left"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-session-refresh"><i class="fa fa-sync"></i></button>
                </div>
@if( $session->pending )
                <div class="btn-group float-right" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-session-open"><i class="fa fa-lock"></i></button>
                </div>
@elseif( $session->opened )
                <div class="btn-group float-right" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-session-close"><i class="fa fa-lock-open"></i></button>
                </div>
@endif
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row">
                <div class="col-md-6 col-sm-12">
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
                </div>
                <div class="col-md-6 col-sm-12">
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
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="meeting-funds">
                </div>
                <div class="col-md-6 col-sm-12" id="meeting-charges">
                </div>
              </div>
@if($tontine->is_financial && $session->opened)
              <div class="row">
                <div class="col-md-6 col-sm-12" id="meeting-biddings">
                </div>
                <div class="col-md-6 col-sm-12" id="meeting-refunds">
                </div>
              </div>
@endif
            </div>
          </div>
