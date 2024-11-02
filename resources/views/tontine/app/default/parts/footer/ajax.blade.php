@jxnJs

@jxnScript

@jxnCss

@php
    $rqTontine = Jaxon\rq(App\Ajax\Web\Tontine\Tontine::class);
    $rqInvite = Jaxon\rq(App\Ajax\Web\Tontine\Invite\Invite::class);
    $rqSession = Jaxon\rq(App\Ajax\Web\Meeting\Session\Session::class);
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
        {!! $rqSession->showBalanceAmounts() !!};
    }
    function showBalanceAmountsWithDelay() {
        setTimeout(() => {!! $rqSession->showBalanceAmounts() !!}, 5);
    }
</script>
