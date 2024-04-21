{!! $jaxonJs !!}

{!! $jaxonScript !!}

{!! $jaxonCss !!}

<script type='text/javascript'>
  const tontine = {
    home: () => {!! $jxnTontine->home() !!},
    users: () => {!! $jxnInvite->home() !!},
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
  function showBalanceAmounts() {
    {!! $jxnSession->showBalanceAmounts() !!};
  }
  function showBalanceAmountsWithDelay() {
    setTimeout(() => {!! $jxnSession->showBalanceAmounts() !!}, 5);
  }
  function makeTableResponsive(tableId)
  {
    const table = document.querySelector('#' + tableId);
    if(!table) {
      return;
    }
    const labels = Array.from(table.querySelectorAll('th')).map(th => th.innerText);
    table.querySelectorAll('td')
      .forEach((td, i) => td.setAttribute('data-label', labels[i % labels.length]));
  }
</script>
