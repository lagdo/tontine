<html>
  <head>
    <style>
      @page {
        size: A4 landscape;
        margin: 3cm 1.1cm 1.2cm 1.1cm;
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
  </head>
  <body>
    <main>
@foreach ($profits as $profit)
      @include('tontine.report.raptor.profits.fund', $profit)
@if (!$loop->last)

      <div class="pagebreak"></div>
@endif
@endforeach
    </main>
  </body>
</html>
