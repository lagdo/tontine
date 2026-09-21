<script src="/assets/app.4.0.14.js"></script>

<script type='text/javascript'>
(function(self) {
    self.labels = {
        amount: "{{ __('common.labels.amount') }}",
        percentage: "{{ __('meeting.loan.labels.percentage') }}",
    };

    self.titles = {
        message: "{{ __('common.titles.message') }}",
    };

    self.home = () => {!! rq(Ajax\Page\Admin::class)->home() !!};

    self.flot = {
        formatLabel: (label, series) => {
            const { data: [[value]] = [[]] } = series;
            return `${label}: ${Number.isInteger(value) && value < 0 ? -value : value}`;
        },
        // formatTickY: (label) => label,
        sessionLabel: (label) => self.labels.session[label] ?? '',
        totalLabel: (label) => self.labels.total[label] ?? '',
    };
})(tontine);
</script>
