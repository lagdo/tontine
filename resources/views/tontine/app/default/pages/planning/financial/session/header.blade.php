              <div class="row" style="flex: 0 0 100%;font-weight:bold;">
                <div class="col-md-4 col-sd-12">
                  {!! __('tontine.pool_round.labels.start_session', [
                    'session' => $pool->start_date . ($pool->pool_round !== null ?
                      '' : ' ' . __('tontine.pool_round.labels.default')),
                  ]) !!}
                </div>
                <div class="col-md-4 col-sd-12">
                  {!! __('tontine.pool_round.labels.end_session', [
                    'session' => $pool->end_date . ($pool->pool_round !== null ?
                      '' : ' ' . __('tontine.pool_round.labels.default')),
                  ]) !!}
                </div>
                <div class="col-md-4 col-sd-12">
                  {!! __('tontine.pool_round.labels.session_count', [
                    'count' => ($pool->counter?->sessions ?? 0) - ($pool->counter?->disabled_sessions ?? 0),
                  ]) !!}
                </div>
              </div>
