@jxnJs

@jxnScript

@jxnCss

@php
    $rqTontine = Jaxon\rq(App\Ajax\Web\Tontine\Tontine::class);
    $rqInvite = Jaxon\rq(App\Ajax\Web\Tontine\Guest\Invite::class);
    $rqSessionMisc = Jaxon\rq(App\Ajax\Web\Meeting\Session\Misc::class);
@endphp
<script type='text/javascript'>
    const tontine = {
        home: () => {!! $rqTontine->home() !!},
        users: () => {!! $rqInvite->home() !!},
        labels: {
            amount: "{{ __('common.labels.amount') }}",
            percentage: "{{ __('meeting.loan.labels.percentage') }}",
        },
        titles: {
            message: "{{ __('common.titles.message') }}",
        },
    };
    function showBalanceAmounts() {
        {!! $rqSessionMisc->showBalanceAmounts() !!};
    }
    function showBalanceAmountsWithDelay() {
        setTimeout(() => {!! $rqSessionMisc->showBalanceAmounts() !!}, 5);
    }
</script>
