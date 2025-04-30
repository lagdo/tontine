@extends('tontine.report.default.layout')

@section('page-title', 'Siak Tontine')

@section('css')
  <style>
    @page {
      size: A4 portrait;
      /*margin: 0;*/
    }
    tbody {
      border-bottom: solid lightgray 1px;
    }
    .table:not(.table-sm):not(.table-md):not(.dataTable) th {
      font-size: 15px;
      vertical-align: top;
    }
    .table:not(.table-sm):not(.table-md):not(.dataTable) td {
      font-size: 16px;
      vertical-align: top;
    }
    th.report-round-pool-amount, td.report-round-pool-amount {
      width: 12%;
      text-align: right;
    }
    th.report-round-pool-count, td.report-round-pool-count {
      width: 5%;
      text-align: right;
    }
    th.report-round-cash-amount {
      width: 10%;
      text-align: right;
      max-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    td.report-round-cash-amount {
      width: 10%;
      text-align: right;
    }
  </style>
@endsection

@section('content')
          <div class="row mt-4">
            <div class="col d-flex justify-content-center">
              <h5>{{ __('tontine.report.titles.credit') }} ({{ $currency }})</h5>
            </div>
          </div>

@foreach ($funds as $fund)
          @include('tontine.report.default.credit.fund', $fund)
@if (!$loop->last)
          <div class="pagebreak"></div>
@endif
@endforeach
@endsection
