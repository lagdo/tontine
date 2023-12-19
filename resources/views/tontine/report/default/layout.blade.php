<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta content="{!! csrf_token() !!}" name="csrf-token" />

  <title>@yield('page-title')</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <!-- Template CSS -->
  <link rel="stylesheet" href="/tpl/assets/css/style.css">

  <style>
    @media print {
      .pagebreak {
        page-break-before: always;
      }
      tr.no-pagebreak {
        break-after: avoid;
        page-break-after: avoid;
      }
    }
    .main-content {
      padding-left: 20px;
      padding-right: 20px;
      padding-top: 10px;
    }
    .table:not(.table-sm):not(.table-md):not(.dataTable) th {
      padding: 0 15px;
      font-size: 16px;
    }
    .table:not(.table-sm):not(.table-md):not(.dataTable) td {
      padding: 0 10px;
      font-size: 18px;
    }
    .language .flag-icon {
      width: 20px;
      height: 12px;
    }
    .table td.table-item-menu {
      width: 90px;
    }
    .table td.table-member-subscription {
      width: 120px;
      padding: 0 10px !important;
    }
    td.table-member-subscription span.input-group-text {
      height: 32px;
    }
    th.currency, td.currency {
      text-align: right;
    }
    #text-session-agenda, #text-session-report, #receivable-notes {
      height: 220px;
    }
    #text-session-notes, #text-session-venue {
      height: 120px;
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

@yield('css')
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">

@yield('content')

        </section>
      </div>

    </div>
  </div>
</body>

</html>
