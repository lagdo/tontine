@php
  $rqSelectFunc = rq(Ajax\App\Tontine\SelectFunc::class);
@endphp
{{ $tontine->name }}
<a class="highlight" role="link" @jxnClick($rqSelectFunc->showOrganisations())><i class="fa fa-caret-square-right"></i></a>
@if($round !== null)
{{ $round->title }}
<a class="highlight" role="link" @jxnClick($rqSelectFunc->showRounds())><i class="fa fa-caret-square-right"></i></a>
@endif
