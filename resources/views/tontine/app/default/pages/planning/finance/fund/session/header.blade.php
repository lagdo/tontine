              <div class="row" style="flex: 0 0 100%;font-weight:bold;">
                <div class="col-md-6 col-sd-12">
                  {!! __('tontine.session.labels.start_session', [
                    'session' => $fund->date_start,
                  ]) !!}
                </div>
                <div class="col-md-6 col-sd-12">
                  {!! __('tontine.session.labels.end_session', [
                    'session' => $fund->date_end,
                  ]) !!}
                </div>
                <div class="col-md-6 col-sd-12">
                  {!! __('tontine.session.labels.count', [
                    'count' => $fund->sessions_count,
                  ]) !!}
                </div>
                <div class="col-md-6 col-sd-12">
                  {!! __('tontine.session.labels.end_interest', [
                    'session' => $fund->date_interest,
                  ]) !!}
                </div>
              </div>
