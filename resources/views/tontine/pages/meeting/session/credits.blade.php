          <div class="section-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}</h2>
              </div>
              <div class="col">
@include('tontine.pages.meeting.session.action', ['session' => $session])
                <div class="btn-group float-right ml-2" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-session-pools">
                    <i class="fa fa-wallet"></i> {{ __('meeting.actions.pools') }}
                  </button>
                  <button type="button" class="btn btn-primary" id="btn-session-charges">
                    <i class="fa fa-money-check"></i> {{ __('meeting.actions.charges') }}
                  </button>
                </div>
@include('tontine.pages.meeting.session.open', ['session' => $session])
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row">
                <div class="col-md-6 col-sm-12">
@include('tontine.pages.meeting.session.agenda', ['session' => $session])
                </div>
                <div class="col-md-6 col-sm-12">
@include('tontine.pages.meeting.session.report', ['session' => $session])
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="meeting-fundings">
                </div>
                <div class="col-md-6 col-sm-12" id="meeting-loans">
                </div>
                <div class="col-md-6 col-sm-12" id="meeting-principal-refunds">
                </div>
                <div class="col-md-6 col-sm-12" id="meeting-interest-refunds">
                </div>
              </div>
            </div>
          </div>
