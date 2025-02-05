<script src="/jaxon/app.4.0.2.js"></script>

@php
    $rqOrganisation = rq(Ajax\App\Admin\Organisation\Organisation::class);
    $rqUser = rq(Ajax\App\Admin\User\User::class);
@endphp
<script type='text/javascript'>
(function(self) {
    self.labels = {
        amount: "{{ __('common.labels.amount') }}",
        percentage: "{{ __('meeting.loan.labels.percentage') }}",
    };

    self.titles = {
        message: "{{ __('common.titles.message') }}",
    };

    self.home = () => {!! $rqOrganisation->home() !!};

    self.users = () => {!! $rqUser->home() !!};
})(Tontine);
</script>
