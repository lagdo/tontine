@extends('tontine::base.layout')

@section('page-title', 'Siak Tontine')

@section('section-title', __('user.titles.users'))

@section('sidebar')
          @include('tontine::parts.sidebar.menu')
@endsection

@section('styles')
@jxnCss()
@endsection

@php
  $rqUser = rq(Ajax\User\User::class);
@endphp

@section('script')
@jxnJs()
@jxnScript()

<script src="/jaxon/app.4.0.10.js"></script>
<script>
(function(self) {
    self.home = () => {!! $rqUser->render() !!};
})(Tontine);
</script>
@endsection

@section('content')
          <div @jxnBind($rqUser)>
          </div>
@endsection
