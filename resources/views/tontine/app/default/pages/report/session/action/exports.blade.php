@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('sqids', 'Sqids\SqidsInterface')
@php
  $rqOptions = rq(Ajax\App\Tontine\Options\Options::class);
@endphp
              <div class="btn-group float-right ml-1">
                <button type="button" class="btn btn-primary" @jxnClick($rqOptions->editOptions())><i class="fa fa-cog"></i></button>
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
