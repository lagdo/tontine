{!! $jaxonJs !!}

{!! $jaxonScript !!}

{!! $jaxonCss !!}

<script type='text/javascript'>
  const tontine = {
    home: () => {!! $jxnTontine->home() !!},
    session: {
      baseUrl: "{{ substr(route('report.session', ['sessionId' => 0]), 0, -1) }}",
    },
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
</script>
<script src="/jaxon/app.js"></script>
