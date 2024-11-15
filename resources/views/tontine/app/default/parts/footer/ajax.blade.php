@jxnJs

@jxnScript

@jxnCss

@php
    $rqOrganisation = Jaxon\rq(Ajax\App\Tontine\Organisation::class);
    $rqInvite = Jaxon\rq(Ajax\App\Tontine\Invite\Invite::class);
    $rqSession = Jaxon\rq(Ajax\App\Meeting\Session\Session::class);
@endphp
<script type='text/javascript'>
    const tontine = {
        home: () => {!! $rqOrganisation->home() !!},
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
