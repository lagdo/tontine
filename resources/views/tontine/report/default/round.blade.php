@inject('locale', 'Siak\Tontine\Service\LocaleService')
@extends('tontine.report.default.layout')

@section('page-title', 'Siak Tontine')

@section('css')
  <style>
    @page {
      size: A4 landscape;
      /*margin: 0;*/
    }
  </style>
@endsection

@section('content')
@foreach ($pools as $pool)
          @include('tontine.report.default.round.pool', $pool->figures)

          <div class="pagebreak"></div>
@endforeach

          @include('tontine.report.default.round.amounts', $amounts)
@endsection
