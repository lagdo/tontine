@extends('tontine.base.layout')

@section('page-title', 'Siak Tontine')

@section('section-title', __('tontine.menus.tontine'))

@section('sidebar')
          @include('tontine.parts.sidebar.menu', ['ajax' => true])
@endsection

@section('content')
          @include('tontine.pages.tontine.home')
@endsection

@section('script')
<script type="text/javascript">
</script>

@include('tontine.parts.footer.ajax', compact('jaxonJs', 'jaxonScript', 'jaxonCss'))
@endsection
