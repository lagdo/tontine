@inject('sqids', 'Sqids\SqidsInterface')
              <div class="btn-group float-right ml-1">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-file-pdf"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" target="_blank" href="{{ route('report.session',
                    ['sessionId' => $sqids->encode([$sessionId])]) }}">{{ __('meeting.actions.report') }}</a>
@if ($hasClosing)
                  <a class="dropdown-item" target="_blank" href="{{ route('report.profits',
                    ['sessionId' => $sqids->encode([$sessionId])]) }}">{{ __('meeting.actions.profits') }}</a>
@endif
                </div>
              </div>
