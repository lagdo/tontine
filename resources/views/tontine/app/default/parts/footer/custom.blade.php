<script src="/jaxon/app.4.0.2.js"></script>

@php
    $rqMenuFunc = rq(Ajax\App\MenuFunc::class);
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

    self.home = () => {!! $rqMenuFunc->admin() !!};
})(Tontine);
</script>
