@inject('locale', 'Siak\Tontine\Service\LocaleService')
@extends('tontine.report.layout')

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
          <div class="row">
            <div class="col-auto">
              <h3>{{ $tontine->name }}</h3>
            </div>
            <div class="col">
              <h3 class="float-right">{{ $country }}</h3>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col d-flex justify-content-center flex-nowrap">
              <h4>{{ $round->title }}</h4>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col">
              <h5 class="section-title">{{ __('figures.titles.amounts') }} ({{ $currency }})</h5>
            </div>
          </div>

@foreach ($pools as $pool)
          @include('tontine.report.round.pool', $pool->figures)

          <div class="pagebreak"></div>
@endforeach

          @include('tontine.report.round.amounts', $amounts)
@endsection
