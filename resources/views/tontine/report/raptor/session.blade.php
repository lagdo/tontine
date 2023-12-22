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
        {{ __('meeting.titles.agenda') }}
      </div>
      <div class="report-text">
        <p>{!! $session->agenda !!}</p>
      </div>

      <div class="section-title">
        {{ __('meeting.titles.report') }}
      </div>
      <div class="report-text">
        <p>{!! $session->report !!}</p>
      </div>

      <div class="pagebreak"></div>

      @include('tontine.report.raptor.session.deposits', $deposits)

      @include('tontine.report.raptor.session.remitments', $remitments)

      @if ($remitments['pools']->filter(function($pool) { return $pool->remit_auction; })->count() > 0)
      @include('tontine.report.raptor.session.auctions', $remitments)
    @endif

      @include('tontine.report.raptor.session.pools', ['session' => $session,
        'pools' => ['deposit' => $deposits['pools'], 'remitment' => $remitments['pools']]])

      <div class="pagebreak"></div>

      @include('tontine.report.raptor.session.bills', $bills)

      <div class="pagebreak"></div>

      @include('tontine.report.raptor.session.disbursements', $disbursements)

      @include('tontine.report.raptor.session.loans', $loans)

      @include('tontine.report.raptor.session.refunds', $refunds)

      @include('tontine.report.raptor.session.savings', $savings)
    </main>
  </body>
</html>
