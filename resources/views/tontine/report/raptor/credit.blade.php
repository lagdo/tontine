<html>
  <head>
    <style>
      @page {
        size: A4;
        margin: 30mm 11mm 15mm 11mm;
      }

      /* 
      Define all colors used in this template 
      */
      :root {
        --font-color: black;
        --highlight-color: #79d5e6;
        --header-bg-color: #B8E6F1;
        --footer-bg-color: #BFC0C3;
        --table-row-separator-color: #BFC0C3;
      }

      .pagebreak {
        page-break-before: always;
      }

      tr.no-pagebreak {
        break-after: avoid;
        page-break-after: avoid;
      }

      body {
        color: var(--font-color);
        font-family: 'Montserrat', sans-serif;
        font-size: 10pt;
      }

      main {
        padding: 0;
      }

      main .section-title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        padding-bottom: 10px;
        color: var(--highlight-color);
        page-break-after: avoid;
      }

      main .table-title {
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        padding-bottom: 10px;
        color:var(--highlight-color);
        page-break-after: avoid;
      }

      main table {
        width: 100%;
        border-collapse: collapse;
      }

      main div.table {
        margin-bottom: 30px;
      }

      main table tbody {
        border-top: solid 1px #e3e5e9;
      }

      main table tbody tr.member {
        border-bottom: solid 1px #e3e5e9;
      }

      main table thead th {
        color: var(--highlight-color);
      }

      main table thead th, main table tbody td {
        text-align: left;
        font-size: 14px;
        padding: 5px 3px;
        vertical-align: top;
        border-bottom: 0.5mm solid var(--table-row-separator-color);
      }

      main table tr.total {
        font-size: 15px;
        font-weight: bold;
        background-color: var(--highlight-color);
      }

      main table tr.total td {
        text-align: right;
        border-bottom: none;
      }
    </style>
  </head>
  <body>
    <main>
      <div class="section-title">
        {{ __('tontine.report.titles.credit') }} ({{ $currency }})
      </div>

@foreach ($funds as $fund)
      @include('tontine.report.raptor.credit.fund', $fund)
@if (!$loop->last)
      <div class="pagebreak"></div>
@endif
@endforeach
    </main>
  </body>
</html>
