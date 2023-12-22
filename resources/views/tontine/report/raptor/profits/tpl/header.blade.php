<html>
  <head>
    <style>
      html {
        -webkit-print-color-adjust: exact;
      }
      body {
        margin: 0;
        font-family: 'Montserrat', sans-serif;
      }
      header {
        width: 277mm;
        padding: 0;
        margin-left: 1cm;
        margin-right: 1cm;
      }
      header .header {
        height: 1.8cm;
        display: flex;
        justify-content: space-between;
        padding: 0 10px;
        background-color: #B8E6F1;
        border-radius: 10px 10px 0 0;
        border: solid 1px ##e3e5e9;
        border-bottom: none;
      }
      header .report {
        text-align: right;
      }
      header .header h2 {
        font-size: 14px;
      }
      header .header p {
        font-size: 12px;
      }
      header div.box {
        height: 173mm;
        border-left: solid 1px #e3e5e9;
        border-right: solid 1px #e3e5e9;
      }
    </style>
  </head>
  <body>
    <header>
      <div class="header">
        <div class="tontine">
          <h2>{{ $tontine->name }}</h2>
          <p>@if(($tontine->city)){{ $tontine->city }} - @endif{{ $country }}</p>
        </div>
        <div class="report">
          <h2>{{ __('tontine.report.titles.profits') }}</h2>
          <p>{{ $session->title }}</p>
        </div>
      </div>
      <div class="box"></div>
    </header>
  </body>
