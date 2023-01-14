@extends('tontine.base.layout')

@section('page-title', 'Siak Tontine')

@section('content')
        <!-- Page heading -->
        <div class="section-header">
          <h1 id="section-title">{{ __('tontine.menus.tontine') }}</h1>
          <div class="section-header-breadcrumb">
            <h3>@isset($tontine){{ $tontine->name }} - {{ $round->title }}@endisset</h3>
          </div>
        </div>
        <div id="content-home">
          @include('tontine.pages.profile.tontine.home')
        </div>
@endsection

@section('script')
<script type="text/javascript">
</script>
@endsection
