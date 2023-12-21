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
    td.report-profits-amount {
      width: 15%;
      text-align: right;
    }
    td.report-profits-count {
      width: 7%;
      text-align: right;
    }
    td.report-profits-session {
      width: 21%;
    }
  </style>
@endsection

@section('content')
@foreach ($profits as $profit)
          @include('tontine.report.default.profits.fund', $profit)
@if (!$loop->last)

          <div class="pagebreak"></div>
@endif
@endforeach
@endsection
