@inject('sqids', 'Sqids\SqidsInterface')
@php
  $rqOptionsFunc = rq(Ajax\App\Guild\Options\OptionsFunc::class);
@endphp
              <div class="btn-group float-right ml-1">
                <button type="button" class="btn btn-primary" @jxnClick($rqOptionsFunc->editOptions())><i class="fa fa-cog"></i></button>
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-file-pdf"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" target="_blank" href="{{ $locale->route('report.session',
                    ['sessionId' => $sqids->encode([$sessionId])]) }}">{{ __('tontine.report.actions.session') }}</a>
                  <a class="dropdown-item" target="_blank" href="{{ $locale->route('report.savings',
                    ['sessionId' => $sqids->encode([$sessionId])]) }}">{{ __('tontine.report.actions.savings') }}</a>
                  <a class="dropdown-item" target="_blank" href="{{ $locale->route('report.credit',
                    ['sessionId' => $sqids->encode([$sessionId])]) }}">{{ __('tontine.report.actions.credit') }}</a>
                </div>
              </div>
