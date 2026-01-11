@extends('tontine_app::base.layout')

@section('page-title', 'Siak Tontine')

@section('page-header')
@php
$rqGuildHeader = rq(Ajax\Page\Header\GuildHeader::class);
$rqSectionHeader = rq(Ajax\Page\Header\SectionHeader::class);
@endphp
          <div class="section-header">
            <div @jxnBind($rqGuildHeader)>
              @jxnHtml($rqGuildHeader)
            </div>
            <div class="mt-2" @jxnBind($rqSectionHeader)>
              @jxnHtml($rqSectionHeader)
            </div>
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
