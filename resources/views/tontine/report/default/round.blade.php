@inject('locale', 'Siak\Tontine\Service\LocaleService')
@extends('tontine.report.default.layout')

@section('page-title', 'Siak Tontine')

@section('css')
  <style>
    @page {
      size: A4 landscape;
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
@foreach ($pools as $pool)
          @include('tontine.report.default.round.pool', $pool)

          <div class="pagebreak"></div>
@endforeach

          @include('tontine.report.default.round.amounts', $amounts)
@endsection
