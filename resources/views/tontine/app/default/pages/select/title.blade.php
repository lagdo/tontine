@php
  $rqSelect = rq(Ajax\App\Tontine\Select::class);
@endphp
{{ $tontine->name }}
<a role="link" @jxnClick($rqSelect->showOrganisations())><i class="fa fa-exchange-alt"></i></a>
@if($round !== null)
{{ $round->title }}
<a role="link" @jxnClick($rqSelect->showRounds())><i class="fa fa-exchange-alt"></i></a>
@endif
