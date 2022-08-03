<!DOCTYPE html>
<html lang="en">

<head>
  @include('parts.header.html')
  <!-- Fix Flag Icon style -->
  <style>
    .language .flag-icon {
      width: 20px;
      height: 12px;
    }
    .modal-header {
      display: block;
    }
    .btn-social-icon {
      color: #212529 !important;
    }
    .section-header-breadcrumb h3 {
      color: #34395e;
      font-size: 18px;
      font-weight: 600;
      margin: 0;
      padding: 0;
      align-self: center;
    }
    .nav-tabs .nav-item .nav-link {
      color: #34395e;
    }
    .nav-tabs .nav-item .nav-link:hover {
      border-color: white;
    }
    .nav-tabs .nav-item .nav-link.active {
      color: #595c5f;
      border-color: white;
      border-bottom-color: #a9acaf;
      border-bottom-width: medium;
    }
    .dropdown-menu a {
      font-size: 16px;
      padding: 4px 24px;
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
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">

@include('parts.header.menu')

      </nav>
      <div class="main-sidebar">

@include('parts.sidebar.menu')

      </div>

      <!-- Main Content -->
      <div class="main-content"><section class="section">

@yield('content')

      </section></div>

    </div>
  </div>
</body>

@include('parts.footer.html')

@include('parts.footer.ajax', compact('jaxonJs', 'jaxonScript', 'jaxonCss'))

@yield('script')
</html>
