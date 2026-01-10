@inject('sqids', 'Sqids\SqidsInterface')
                <div class="btn-group ml-2">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-alt"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.session', [
                        'sessionId' => $sqids->encode([$sessionId]),
                      ]) }}">{{ __('meeting.entry.actions.session') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form', [
                        'form' => 'report',
                        'sessionId' => $sqids->encode([$sessionId]),
                      ]) }}">{{ __('meeting.entry.actions.report') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form', [
                        'form' => 'transactions',
                        'sessionId' => $sqids->encode([$sessionId]),
                      ]) }}">{{ __('meeting.entry.actions.transactions') }}</a>
                  </div>
                </div>
