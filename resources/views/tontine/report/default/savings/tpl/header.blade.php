<html>
  <head>
    <style>
      header {
        width: 100%;
        margin-top: -5px;
        margin-left: 40px;
        margin-right: 40px;
        padding-bottom: 5px;
        border-bottom: solid lightgray 1px;
      }
      .first-line {
        font-size: 13px;
      }
      .second-line {
        margin-top: 5px;
        font-size: 11px;
      }
      .right-col {
        float: right;
      }
    </style>
  </head>
  <body>
    <header>
      <div class="first-line">
        <span>{{ $tontine->name }}</span>
        <span class="right-col">{{ __('tontine.report.titles.savings') }}</span>
      </div>
      <div class="second-line">
        <span>@if ($tontine->city){{ $tontine->city }} - @endif{{ $country }}</span>
        <span class="right-col">{{ $session->title }}</span>
      </div>
    </header>
  </body>
</html>
