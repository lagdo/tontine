jaxon.dom.ready(function() {
    Tontine.home();

    jaxon.ajax.callback.tontine = {
        onRequest: function() {
            document.body.style.cursor = 'wait';
        },
        onComplete: function() {
            document.body.style.cursor = 'auto';
        },
        onFailure: function() {
            document.body.style.cursor = 'auto';
        },
    };

    jaxon.ajax.callback.selectCurrency = {
        onRequest: function() {
            // Empty the currency list while fetching the new currencies.
            $('#select_currency_container select').html('');
        },
    };
});

var Tontine = {};
(function(self) {
    self.setLoanInterestLabel = () => {
        $('#loan-interest-type').change(() => {
            const type = $('#loan-interest-type').val();
            $('#loan-interest-label').html(type === 'f' ?
                self.labels.amount : self.labels.percentage);
        });
    };

    // Get the labels for tables cells, taking the colspan attr into account.
    const headerLabels = (tableHeaders) => {
        const labels = [];
        Array.from(tableHeaders).forEach((header) => {
            const colCount = parseInt(header.getAttribute('colspan') ?? '1');
            for (let i = 0; i < colCount; i++) {
                labels.push(header.innerHTML.replace('-<br>', '')
                    .replace('<br>', ' ').replace('&nbsp;', ' '));
            }
        });
        return labels;
    };

    const makeTableResponsive = (table) => {
        const labels = headerLabels(table.querySelectorAll('th'));
        table.querySelectorAll('td').forEach((td, i) => td
            .setAttribute('data-label', labels[i % labels.length]));
    };

    self.makeTableResponsive = (wrapperId) => {
        document.querySelectorAll(`#${wrapperId} table`).forEach((table) => makeTableResponsive(table));
    };

    self.showSmScreen = (targetId, wrapperId) => {
        $('.sm-screen', $('#' + wrapperId)).removeClass('sm-screen-active');
        $('#' + targetId).addClass('sm-screen-active');
    };

    self.setSmScreenHandler = (btnWrapperId, screensWrapperId = 'content-home') => {
        const btnWrapper = $('#' + btnWrapperId);
        $('button', btnWrapper).click(function() {
            const targetId = $(this).attr('data-target');
            if(!targetId) {
                return;
            }
            // Show the target screen.
            self.showSmScreen(targetId, screensWrapperId);
            // Activate the button the user has clicked on.
            $('button', btnWrapper).removeClass('btn-primary');
            $('button', btnWrapper).removeClass('btn-outline-primary');
            $('button', btnWrapper).addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary');
            $(this).addClass('btn-primary');
        });
    };
})(Tontine);
