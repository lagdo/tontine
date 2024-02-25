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

      main .table-title {
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        padding-bottom: 5px;
        color:var(--highlight-color);
        page-break-after: avoid;
      }

      main table {
        width: 100%;
        border-collapse: collapse;
      }

      main div.table {
        margin-bottom: 20px;
      }

      main table thead th {
        color: var(--highlight-color);
      }

      main table thead th, main table tbody td {
        text-align: left;
        font-size: 14px;
        padding: 5px 3px;
        vertical-align: top;
      }

      main table tr.row td, main table tr.row th {
        border-bottom: 0.3mm solid var(--table-row-separator-color);
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
@foreach ([0, 1, 2, 3] as $_)
      <div class="table-title">
        <table>
          <thead>
            <tr>
              <th><input type="checkbox" />{{ __('meeting.titles.fees') }}</th>
              <th><input type="checkbox" />{{ __('meeting.titles.fines') }}</th>
              <th><input type="checkbox" />{{ __('meeting.titles.loans') }}</th>
              <th><input type="checkbox" />{{ __('meeting.titles.refunds') }}</th>
              <th><input type="checkbox" />{!! __('meeting.titles.savings') !!}</th>
              <th><input type="checkbox" />{{ __('meeting.titles.disbursements') }}</th>
              <th><input type="checkbox" />{{ __('meeting.remitment.titles.auctions') }}</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="table-title">
        <table>
          <thead>
            <tr>
              <th>{{ __('meeting.labels.item') }}:</th>
              <th style="width:30%;">{{ __('common.labels.amount') }}: </th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="table">
        <table>
          <thead>
            <tr class="row">
              <th>{{ __('meeting.labels.member') }}</th>
              <th style="width:35%;">{{ __('meeting.labels.item') }}</th>
              <th style="width:15%;">{{ __('common.labels.amount') }}</th>
              <th style="width:5%;text-align:right;">{{ __('common.labels.paid') }}</th>
            </tr>
          </thead>
          <tbody>
@foreach ([0, 1, 2, 3, 4] as $_)
            <tr class="row">
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td style="text-align:right;"><input type="checkbox" /></td>
            </tr>
@endforeach
          </tbody>
        </table>
      </div> <!-- End table -->
@endforeach
    </main>
  </body>
</html>
