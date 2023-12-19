@inject('sqids', 'Sqids\SqidsInterface')
                <div class="btn-group float-right ml-1" role="group">
                  <a type="button" class="btn btn-primary" target="_blank" href="{{ route('report.session',
                    ['sessionId' => $sqids->encode([$sessionId])]) }}"><i class="fa fa-file-pdf"> {{ __('meeting.actions.report') }}</i></a>
@if ($hasClosing)
                  <a type="button" class="btn btn-primary" target="_blank" href="{{ route('report.profits',
                    ['sessionId' => $sqids->encode([$sessionId])]) }}"><i class="fa fa-file-pdf"> {{ __('meeting.actions.profits') }}</i></a>
@endif
              </div>
