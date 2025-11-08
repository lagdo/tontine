<script src="/jaxon/app.4.0.12.js"></script>

<script type='text/javascript'>
(function(self) {
    self.labels = {
        amount: "{{ __('common.labels.amount') }}",
        percentage: "{{ __('meeting.loan.labels.percentage') }}",
    };

    self.titles = {
        message: "{{ __('common.titles.message') }}",
    };

    self.home = () => {!! rq(Ajax\Page\MenuFunc::class)->admin() !!};
})(Tontine);
</script>
