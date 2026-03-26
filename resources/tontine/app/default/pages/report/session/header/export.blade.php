@php
  $rqOptionsFunc = rq(Ajax\App\Guild\Options\OptionsFunc::class);
@endphp
                <div class="btn-group ml-1">
                  <button type="button" class="btn btn-primary" @jxnClick($rqOptionsFunc->editOptions())>
                    <i class="fa fa-cog"></i>
                  </button>
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-pdf"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('report.session', [
                      'guildId' => $guildId,
                      'sessionId' => $sessionId,
                    ]) }}">{{ __('tontine.report.actions.session') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('report.savings', [
                      'guildId' => $guildId,
                      'sessionId' => $sessionId,
                    ]) }}">{{ __('tontine.report.actions.savings') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('report.credit', [
                      'guildId' => $guildId,
                      'sessionId' => $sessionId,
                    ]) }}">{{ __('tontine.report.actions.credit') }}</a>
                  </div>
                </div>
                <div class="btn-group ml-2">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-alt"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.session', [
                      'guildId' => $guildId,
                      'sessionId' => $sessionId,
                    ]) }}">{{ __('meeting.entry.actions.session') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form', [
                      'form' => 'report',
                      'guildId' => $guildId,
                      'sessionId' => $sessionId,
                    ]) }}">{{ __('meeting.entry.actions.report') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form', [
                      'form' => 'transactions',
                      'guildId' => $guildId,
                      'sessionId' => $sessionId,
                    ]) }}">{{ __('meeting.entry.actions.transactions') }}</a>
                  </div>
                </div>
