@inject('tenant', Siak\Tontine\Service\TenantService::class)
@inject('locale', Siak\Tontine\Service\LocaleService::class)
@php
  $rqMenuFunc = rq(Ajax\App\MenuFunc::class);
@endphp
<a class="highlight" role="link" @jxnClick($rqMenuFunc->admin())><i class="fa fa-caret-square-left"></i></a>

@if($tenant->user()->tontines()->count() > 0)
@php
  $tontine = $tenant->tontine();
  $round = $tenant->round();
@endphp
{{ $tontine?->name ?? __('tontine.titles.select.tontine') }}
@if ($tontine !== null)({{ $locale->getCurrencyName() }})@endif
<a class="highlight" role="link" @jxnClick($rqMenuFunc->showOrganisations())><i class="fa fa-caret-square-right"></i></a>

@if($tontine !== null && $tontine->rounds()->count() > 0)
{{ $round?->title ?? __('tontine.titles.select.round') }}
<a class="highlight" role="link" @jxnClick($rqMenuFunc->showRounds())><i class="fa fa-caret-square-right"></i></a>
@endif
@endif
