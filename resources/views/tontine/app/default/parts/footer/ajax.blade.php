@jxnJs

@jxnScript

@jxnCss

@php
    $rqOrganisation = rq(Ajax\App\Admin\Organisation\Organisation::class);
    $rqUser = rq(Ajax\App\Admin\User\User::class);
    $rqSession = rq(Ajax\App\Meeting\Session\Session::class);
@endphp
<script type='text/javascript'>
    const tontine = {
        home: () => {!! $rqOrganisation->home() !!},
        users: () => {!! $rqUser->home() !!},
        labels: {
            amount: "{{ __('common.labels.amount') }}",
            percentage: "{{ __('meeting.loan.labels.percentage') }}",
        },
        titles: {
            message: "{{ __('common.titles.message') }}",
        },
    };
</script>
