@inject('tenant', Siak\Tontine\Service\TenantService::class)
@inject('locale', Siak\Tontine\Service\LocaleService::class)
@php
  $rqMenuFunc = rq(Ajax\App\MenuFunc::class);
  $tontine = $tenant->tontine();
  $round = $tenant->round();
  $tontineCount = $tenant->user()->tontines()->count();
  $roundCount = $tontine?->rounds()->count() ?? 0;
@endphp
            <div class="row">
              <div class="col">
                <h2>
                  @if($tontineCount > 0)<a class="highlight" role="link" @jxnClick($rqMenuFunc
                    ->showOrganisations())><i class="fa fa-caret-square-right"></i></a>@endif
                  {{ $tontine?->name ?? __('tontine.titles.select.tontine') }}
                </h2>
              </div>
              <div class="col-auto">
                @if ($tontine !== null)<h2>{{ $locale->getCurrencyName() }} <i class="fa fa-money-bill"></i></h2>@else &nbsp;@endif
              </div>
            </div>
            <div class="row">
              <div class="col">
                <h2>
                  @if($roundCount > 0)<a class="highlight" role="link" @jxnClick($rqMenuFunc
                    ->showRounds())><i class="fa fa-caret-square-right"></i></a>@endif
                  {{ $round?->title ?? __('tontine.titles.select.round') }}
                </h2>
              </div>
              <div class="col-auto section-header-title">
                <h3 @jxnBind(rq(Ajax\App\Page\SectionTitle::class))>@yield('section-title')</h3>
              </div>
            </div>
