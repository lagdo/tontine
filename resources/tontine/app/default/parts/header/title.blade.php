@inject('locale', Siak\Tontine\Service\LocaleService::class)
@php
  $rqMenuFunc = rq(Ajax\Page\MenuFunc::class);
@endphp
            <div class="row">
              <div class="col-auto">
                <h2>
                  @if($guildCount > 0)<a class="highlight" role="link" @jxnClick($rqMenuFunc
                    ->showGuilds())><i class="fa fa-caret-square-right"></i></a>@endif
                  {{ $guild?->name ?? __('tontine.titles.select.guild') }}
                </h2>
              </div>
              <div class="col d-flex align-items-end">
                <h2 class="ml-auto">
                  @if($roundCount > 0)<a class="highlight" role="link" @jxnClick($rqMenuFunc
                    ->showRounds())><i class="fa fa-caret-square-right"></i></a>@endif
                  {{ $round?->title ?? __('tontine.titles.select.round') }}
                </h2>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-auto">
                <h2 @jxnBind(rq(Ajax\Page\SectionTitle::class))>{{ __('tontine.menus.admin') }}</h2>
              </div>
              <div class="col d-flex align-items-end section-header-title">
@if ($guild !== null)
                <h3 class="ml-auto"><i class="fa fa-money-bill"></i> {{ $locale->getCurrencyName() }}</h3>
@endif
              </div>
            </div>
