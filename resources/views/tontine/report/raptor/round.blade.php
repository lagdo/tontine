<html>
  <head>
    <style>
      @page {
        size: A4 landscape;
        margin: 30mm 11mm 15mm 11mm;
      }

      /* 
      Define all colors used in this template 
      */
      :root {
        --font-color: black;
        --highlight-color: #60D0E4;
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
  </head>
  <body>
    <main>
      <div class="section-title">
        {{ __('figures.titles.amounts') }} ({{ $currency }})
      </div>

@foreach ($pools as $pool)
      @include('tontine.report.raptor.round.pool', $pool)

      <div class="pagebreak"></div>
@endforeach

      @include('tontine.report.raptor.round.amounts', $amounts)
    </main>
  </body>
</html>
