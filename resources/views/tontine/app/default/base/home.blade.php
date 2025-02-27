@extends('tontine::base.layout')

@section('page-title', 'Siak Tontine')

@section('section-title', __('tontine.menus.tontine'))

@section('styles')
@include('tontine::parts.header.jaxon')
@endsection

@section('sidebar')
          @include('tontine::parts.sidebar.menu', ['ajax' => true])
@endsection

@section('content')
          @include('tontine::pages.tontine.home')
@endsection

@section('script')
@include('tontine::parts.footer.jaxon')
@endsection
