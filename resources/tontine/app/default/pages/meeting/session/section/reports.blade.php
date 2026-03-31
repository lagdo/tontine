@php
  $agendaText = jq('#session-agenda')->summernote('code');
  $reportText = jq('#session-report')->summernote('code');
  $rqSessionFunc = rq(Ajax\App\Meeting\Session\SessionFunc::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
@include('tontine_app::pages.meeting.session.section.action', [
  'sectionTitle' => __('meeting.actions.reports'),
])
            </div>
          </div>

          <div class="row sm-screen-selector mb-2" id="session-reports-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="session-reports-agenda" type="button" class="btn btn-primary">
                  {!! __('meeting.titles.agenda') !!}
                </button>
                <button data-target="session-reports-report" type="button" class="btn btn-outline-primary">
                  {!! __('meeting.titles.report') !!}
                </button>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="session-reports-agenda">
              <div class="card shadow">
                <div class="card-body">
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.agenda') !!}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->saveAgenda($agendaText))><i class="fa fa-save"></i></button>
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
              </div>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="session-reports-report">
              <div class="card shadow">
                <div class="card-body">
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.report') !!}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->saveReport($reportText))><i class="fa fa-save"></i></button>
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
