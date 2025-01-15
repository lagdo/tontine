@php
  $rqSelect = rq(Ajax\App\Tontine\Select::class);
@endphp
{{ $tontine->name }}
<a class="highlight" role="link" @jxnClick($rqSelect->showOrganisations())><i class="fa fa-caret-square-right"></i></a>
@if($round !== null)
{{ $round->title }}
<a class="highlight" role="link" @jxnClick($rqSelect->showRounds())><i class="fa fa-caret-square-right"></i></a>
@endif
