@extends('tontine.report.layout')

@section('page-title', 'Siak Tontine')

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
              <h4>{{ $session->title }}</h4>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col">
              <h6 class="section-title">{{ __('meeting.titles.agenda') }}</h6>
              <p>{!! $session->agenda !!}</p>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col">
              <h6 class="section-title">{{ __('meeting.titles.report') }}</h6>
              <p>{!! $session->report !!}</p>
            </div>
          </div>

          <div class="pagebreak"></div>

          @include('tontine.report.session.deposits', $deposits)

          @include('tontine.report.session.remitments', $remitments)

@if ($remitments['pools']->filter(function($pool) { return $pool->remit_auction; })->count() > 0)
          @include('tontine.report.session.auctions', $remitments)
@endif

          @include('tontine.report.session.pools', ['session' => $session,
            'pools' => ['deposit' => $deposits['pools'], 'remitment' => $remitments['pools']]])

          <div class="pagebreak"></div>

          @include('tontine.report.session.bills', $bills)

          <div class="pagebreak"></div>

          @include('tontine.report.session.disbursements', $disbursements)

          @include('tontine.report.session.loans', $loans)

          @include('tontine.report.session.refunds', $refunds)

          @include('tontine.report.session.savings', $savings)

@if ($profits['show'])
          <div class="pagebreak"></div>

          @include('tontine.report.session.profits', $profits)
@endif
@endsection
