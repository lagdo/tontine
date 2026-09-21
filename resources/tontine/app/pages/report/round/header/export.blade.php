@php
  $rqOptionsFunc = rq(Ajax\App\Guild\Options\OptionsFunc::class);
@endphp
                <div class="btn-group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqOptionsFunc->editOptions())>
                    <i class="fa fa-cog"></i>
                  </button>
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-pdf"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('report.round', [
                      'guildId' => $guildId,
                      'roundId' => $roundId,
                    ]) }}">{{ __('tontine.report.actions.round') }}</a>
                  </div>
                </div>
                <div class="btn-group ml-3">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-alt"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form', [
                      'guildId' => $guildId,
                      'form' => 'report',
                    ]) }}">{{ __('meeting.entry.actions.report') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form', [
                      'guildId' => $guildId,
                      'form' => 'transactions',
                    ]) }}">{{ __('meeting.entry.actions.transactions') }}</a>
                  </div>
                </div>
