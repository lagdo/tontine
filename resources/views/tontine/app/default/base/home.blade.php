@extends('tontine::base.layout')

@section('page-title', 'Siak Tontine')

@section('page-header')
@php
$rqMainTitle = rq(Ajax\Page\MainTitle::class);
@endphp
          <div class="section-header" @jxnBind($rqMainTitle)>
            @jxnHtml($rqMainTitle)
          </div>
@endsection

@section('section-title', __('tontine.menus.tontine'))

@section('styles')
@include('tontine::parts.header.jaxon')
@endsection

@section('sidebar')
          @include('tontine::parts.sidebar.menu')
@endsection

@section('content')
          <div id="content-home" @jxnBind(rq(Ajax\Page\SectionContent::class))>
            @include('tontine::pages.admin.guild.home')
          </div>
@endsection

@section('script')
@include('tontine::parts.footer.jaxon')
@endsection
