              <div class="row" style="flex: 0 0 100%;font-weight:bold;">
                <div class="col-md-6 col-sm-12">
                  {!! __('tontine.session.labels.start_session', [
                    'session' => $fund->date('start_date'),
                  ]) !!}
                </div>
                <div class="col-md-6 col-sm-12">
                  {!! __('tontine.session.labels.end_session', [
                    'session' => $fund->date('end_date'),
                  ]) !!}
                </div>
                <div class="col-md-6 col-sm-12">
                  {!! __('tontine.session.labels.count', [
                    'count' => $fund->sessions_count,
                  ]) !!}
                </div>
                <div class="col-md-6 col-sm-12">
                  {!! __('tontine.session.labels.end_interest', [
                    'session' => $fund->date('interest_date'),
                  ]) !!}
                </div>
              </div>
