@extends('tontine.app.default.base.layout')

@section('page-title', 'Siak Tontine')

@section('section-title', __('tontine.menus.tontine'))

@section('sidebar')
          @include('tontine.app.default.parts.sidebar.menu', ['ajax' => true])
@endsection

@section('content')
          @include('tontine.app.default.pages.tontine.home', [
            'rqTontine' => Jaxon\rq(App\Ajax\Web\Tontine\Tontine::class),
            'rqTontinePage' => Jaxon\rq(App\Ajax\Web\Tontine\TontinePage::class),
            'rqTontineGuest' => Jaxon\rq(App\Ajax\Web\Tontine\Guest\Tontine::class),
            'rqSelect' => Jaxon\rq(App\Ajax\Web\Tontine\Select::class),
          ])
@endsection

@section('script')
  <script type="text/javascript">
  </script>

@include('tontine.app.default.parts.footer.ajax')

  <script src="/jaxon/app.3.4.1.js"></script>
@endsection
