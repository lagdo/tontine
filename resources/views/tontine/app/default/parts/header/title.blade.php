@inject('tenant', Siak\Tontine\Service\TenantService::class)
@inject('locale', Siak\Tontine\Service\LocaleService::class)
@php
  $rqMenuFunc = rq(Ajax\App\MenuFunc::class);
  $guild = $tenant->guild();
  $round = $tenant->round();
  $guildCount = $tenant->user()->guilds()->count();
  $roundCount = $guild?->rounds()->count() ?? 0;
@endphp
            <div class="row">
              <div class="col-auto">
                <h2>
                  @if($guildCount > 0)<a class="highlight" role="link" @jxnClick($rqMenuFunc
                    ->showGuilds())><i class="fa fa-caret-square-right"></i></a>@endif
                  {{ $guild?->name ?? __('tontine.titles.select.guild') }}
                </h2>
              </div>
@if ($guild !== null)
              <div class="col d-flex align-items-end">
                <h2 class="ml-auto">{{ $locale->getCurrencyName() }} <i class="fa fa-money-bill"></i></h2>
              </div>
@endif
            </div>
            <div class="row">
              <div class="col-auto">
                <h2>
                  @if($roundCount > 0)<a class="highlight" role="link" @jxnClick($rqMenuFunc
                    ->showRounds())><i class="fa fa-caret-square-right"></i></a>@endif
                  {{ $round?->title ?? __('tontine.titles.select.round') }}
                </h2>
              </div>
              <div class="col section-header-title d-flex align-items-end">
                <h3 class="ml-auto" @jxnBind(rq(Ajax\App\Page\SectionTitle::class))>@yield('section-title')</h3>
              </div>
            </div>
