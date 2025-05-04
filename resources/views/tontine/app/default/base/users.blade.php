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
  $rqHostUser = rq(Ajax\User\Host\Host::class);
  $rqGuestUser = rq(Ajax\User\Guest\Guest::class);
@endphp

@section('script')
@jxnJs()
@jxnScript()

<script>
jaxon.dom.ready(function() {
  {!! $rqHostUser->render() !!};
  {!! $rqGuestUser->render() !!};
});
</script>
@endsection

@section('content')
          <div class="row sm-screen-selector mt-2 mb-1" id="invites-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="content-host-invites-home" type="button" class="btn btn-primary">
                  {{ __('tontine.invite.titles.hosts') }}
                </button>
                <button data-target="content-guest-invites-home" type="button" class="btn btn-outline-primary">
                  {{ __('tontine.invite.titles.guests') }}
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div @jxnBind($rqHostUser) class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-host-invites-home">
            </div>
            <div @jxnBind($rqGuestUser) class="col-md-6 col-sm-12 sm-screen" id="content-guest-invites-home">
            </div>
          </div>
@endsection
