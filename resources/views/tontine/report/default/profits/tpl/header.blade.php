<html>
  <head>
    <style>
      header {
        width: 100%;
        margin-top: -5px;
        margin-left: 56px;
        margin-right: 56px;
        padding-bottom: 5px;
        border-bottom: solid lightgray 1px;
      }
      .first-line {
        font-size: 20px;
      }
      .second-line {
        margin-top: 5px;
        font-size: 15px;
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
        <span class="right-col">{{ __('tontine.report.titles.profits') }}</span>
      </div>
      <div class="second-line">
        <span>@if ($tontine->city){{ $tontine->city }} - @endif{{ $country }}</span>
        <span class="right-col">{{ $session->title }}</span>
      </div>
    </header>
  </body>
</html>
