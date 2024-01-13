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

      main .section {
        text-align: center;
        font-weight: bold;
        padding-bottom: 10px;
        color: var(--highlight-color);
        page-break-after: avoid;
      }

      main .section-title {
        font-size: 18px;
      }

      main .section-subtitle {
        font-size: 15px;
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

      main table th {
        text-align: left;
      }

      td.report-savings-amount {
        width: 15%;
        text-align: right;
      }
      td.report-savings-count {
        width: 7%;
        text-align: right;
      }
      td.report-savings-session {
        width: 21%;
      }
    </style>
  </head>
  <body>
    <main>
@foreach ($funds as $fund)
      @include('tontine.report.raptor.savings.fund', $fund)
@if (!$loop->last)

      <div class="pagebreak"></div>
@endif
@endforeach
    </main>
  </body>
</html>
