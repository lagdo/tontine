              <div class="row" style="flex: 0 0 100%;font-weight:bold;">
                <div class="col-md-6 col-sd-12">
                  {!! __('tontine.session.labels.start_session', [
                    'session' => $pool->start_date,
                  ]) !!}
                </div>
                <div class="col-md-6 col-sd-12">
                  {!! __('tontine.session.labels.end_session', [
                    'session' => $pool->end_date,
                  ]) !!}
                </div>
                <div class="col-md-6 col-sd-12">
                  {!! __('tontine.session.labels.count', [
                    'count' => $pool->sessions_count - $pool->disabled_sessions_count,
                  ]) !!}
                </div>
              </div>
