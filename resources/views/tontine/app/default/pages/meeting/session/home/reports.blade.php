@include('tontine.app.default.pages.meeting.session.menu.wrapper', ['session' => $session])
@php
  $agendaText = Jaxon\jq('#session-agenda')->summernote('code');
  $reportText = Jaxon\jq('#session-report')->summernote('code');
  $rqSession = Jaxon\rq(App\Ajax\Web\Meeting\Session\Session::class);
@endphp
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.agenda') !!}</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSession->saveAgenda($agendaText))><i class="fa fa-save"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div id="session-agenda">
                        {!! $session->agenda !!}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12">
                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.report') !!}</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSession->saveReport($reportText))><i class="fa fa-save"></i></button>
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
                </div>
              </div>
            </div>
          </div>
