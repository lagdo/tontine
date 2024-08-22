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
    };
    function showBalanceAmounts() {
        {!! $jxnSessionMisc->showBalanceAmounts() !!};
    }
    function showBalanceAmountsWithDelay() {
        setTimeout(() => {!! $jxnSessionMisc->showBalanceAmounts() !!}, 5);
    }
</script>
