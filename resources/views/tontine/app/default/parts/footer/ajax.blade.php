{!! $jaxonJs !!}

{!! $jaxonScript !!}

{!! $jaxonCss !!}

<script type='text/javascript'>
  // Localized Jaxon request processing URI
  jaxon.config.requestURI = '{!! $jxnRequestUri !!}';

  const tontine = {
    home: () => {!! $jxnTontine->home() !!},
    labels: {
      amount: "{{ __('common.labels.amount') }}",
      percentage: "{{ __('meeting.loan.labels.percentage') }}",
    },
    titles: {
      message: "{{ __('common.titles.message') }}",
    },
    messages: {
      orientation: "{{ __('tontine.messages.screen.orientation') }}",
    },
  };
  function showBalanceAmounts() { {!! $jxnSession->showBalanceAmounts() !!}; }
  function showBalanceAmountsWithDelay() { setTimeout(() => {!! $jxnSession->showBalanceAmounts() !!}, 5); }
</script>
<script src="/jaxon/app.js"></script>
