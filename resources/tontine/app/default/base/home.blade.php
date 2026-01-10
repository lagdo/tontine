@extends('tontine_app::base.layout')

@section('page-title', 'Siak Tontine')

@section('page-header')
@php
$rqMainTitle = rq(Ajax\Page\MainTitle::class);
@endphp
          <div class="section-header" @jxnBind($rqMainTitle)>
            @jxnHtml($rqMainTitle)
          </div>
@endsection

@section('styles')
@include('tontine_app::parts.header.jaxon')
@endsection

@section('sidebar')
          @include('tontine_app::parts.sidebar.menu')
@endsection

@section('content')
          <div id="content-home" @jxnBind(rq(Ajax\Page\SectionContent::class))>
            @include('tontine_app::pages.admin.guild.home')
          </div>
@endsection

@section('script')
@include('tontine_app::parts.footer.jaxon')
@endsection
