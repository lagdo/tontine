@extends('tontine.app.default.base.layout')

@section('page-title', 'Siak Tontine')

@section('section-title', __('tontine.menus.tontine'))

@section('sidebar')
          @include('tontine.app.default.parts.sidebar.menu', ['ajax' => true])
@endsection

@section('content')
          @include('tontine.app.default.pages.tontine.home')
@endsection

@section('script')
  <script type="text/javascript">
  </script>

@include('tontine.app.default.parts.footer.ajax')

  <script src="/jaxon/app.4.0.0.js"></script>
@endsection
