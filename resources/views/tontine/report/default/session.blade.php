@extends('tontine.report.default.layout')

@section('page-title', 'Siak Tontine')

@section('css')
  <style>
    @page {
      size: A4 portrait;
      /*margin: 0;*/
    }
    .table:not(.table-sm):not(.table-md):not(.dataTable) th {
      font-size: 15px;
      vertical-align: top;
    }
    .table:not(.table-sm):not(.table-md):not(.dataTable) td {
      font-size: 16px;
      vertical-align: top;
    }
  </style>
@endsection

@section('content')
          <div class="row mt-4">
            <div class="col d-flex justify-content-center">
              <h5>{{ __('meeting.titles.agenda') }}</h5>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <p>{!! $session->agenda !!}</p>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col d-flex justify-content-center">
              <h5>{{ __('meeting.titles.report') }}</h5>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <p>{!! $session->report !!}</p>
            </div>
          </div>

          <div class="pagebreak"></div>

          @include('tontine.report.default.session.deposits', $deposits)

          @include('tontine.report.default.session.remitments', $remitments)

@if ($remitments['pools']->filter(function($pool) { return $pool->remit_auction; })->count() > 0)
          @include('tontine.report.default.session.auctions', $remitments)
@endif

          @include('tontine.report.default.session.pools', ['session' => $session,
            'pools' => ['deposit' => $deposits['pools'], 'remitment' => $remitments['pools']]])

          <div class="pagebreak"></div>

          @include('tontine.report.default.session.bills', $bills)

          <div class="pagebreak"></div>

          @include('tontine.report.default.session.disbursements', $disbursements)

          @include('tontine.report.default.session.loans', $loans)

          @include('tontine.report.default.session.refunds', $refunds)

          @include('tontine.report.default.session.savings', $savings)
@endsection
