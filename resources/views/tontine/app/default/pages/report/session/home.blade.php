@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('sqids', 'Sqids\SqidsInterface')
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title" id="session-report-title"></h2>
              </div>
              <div class="col-auto">
                <div class="input-group">
                  {{ Form::select('session_id', $sessions, 0, ['class' => 'form-control', 'id' => 'select-session']) }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" id="btn-session-select"><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
@if ($sessionId > 0)
              <div class="col-auto">
                <div class="btn-group float-right ml-1">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-alt"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.session',
                      ['sessionId' => $sqids->encode([$sessionId])]) }}">{{ __('meeting.entry.actions.session') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form',
                      ['form' => 'report', 'sessionId' => $sqids->encode([$sessionId])]) }}">{{ __('meeting.entry.actions.report') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form',
                      ['form' => 'transactions', 'sessionId' => $sqids->encode([$sessionId])]) }}">{{ __('meeting.entry.actions.transactions') }}</a>
                  </div>
                </div>
              </div>
@endif
              <div class="col-auto" id="session-reports-export">
              </div>
              <div class="col-auto">
                <div class="input-group">
                  {{ Form::select('member_id', $members, 0, ['class' => 'form-control', 'id' => 'select-member']) }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" id="btn-member-select"><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-deposits">
                </div>
                <div class="col-md-6 col-sm-12" id="report-remitments">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-session-bills">
                </div>
                <div class="col-md-6 col-sm-12" id="report-total-bills">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-disbursements">
                </div>
                <div class="col-md-6 col-sm-12" id="report-loans">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-refunds">
                </div>
                <div class="col-md-6 col-sm-12" id="report-savings">
                </div>
              </div>
              <div class="row">
                <div class="col-12" id="report-fund-savings">
                </div>
              </div>
            </div>
          </div>
