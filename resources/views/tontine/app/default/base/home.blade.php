@extends('tontine.app.default.base.layout')

@section('page-title', 'Siak Tontine')

@section('section-title', __('tontine.menus.tontine'))

@section('styles')
@include('tontine.app.default.parts.header.jaxon')
@endsection

@section('sidebar')
          @include('tontine.app.default.parts.sidebar.menu', ['ajax' => true])
@endsection

@section('content')
          @include('tontine.app.default.pages.tontine.home')
@endsection

@section('script')
@include('tontine.app.default.parts.footer.jaxon')
@endsection
