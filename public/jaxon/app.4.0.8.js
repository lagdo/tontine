jaxon.dom.ready(function() {
    Tontine.home();

    const spin = {
        spinner: new Spin.Spinner({ top: '50%' }),
        count: 0, // To make sure that the spinner is started once.
    };
    jaxon.ajax.callback.tontine = {
        onRequest: function() {
            if(spin.count++ === 0)
            {
                spin.spinner.spin(document.getElementById('main-section'));
            }
        },
        onComplete: function() {
            if(--spin.count === 0)
            {
                spin.spinner.stop();
            }
        },
        onFailure: function() {
            if(--spin.count === 0)
            {
                spin.spinner.stop();
            }
        },
    };

    jaxon.ajax.callback.selectCurrency = {
        ...jaxon.ajax.callback.tontine,
        // Empty the currency list while fetching the new currencies.
        onRequest: () => {
            jaxon.ajax.callback.tontine.onRequest();
            $('#select_currency_container select').html('');
        },
    };

    jaxon.ajax.callback.hideMenuOnMobile = {
        ...jaxon.ajax.callback.tontine,
        // Hide the sidebar menu on mobile devices
        onRequest: () => {
            jaxon.ajax.callback.tontine.onRequest();
            $('body').trigger('touchend');
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
            const colSpan = parseInt(header.getAttribute('colspan') ?? '1');
            // The label can be duplicated depending on the colspan attribute.
            const colLabel = header.innerHTML.replace('-<br>', '')
                .replace('<br>', ' ').replace('&nbsp;', ' ');
            for (let i = 0; i < colSpan; i++) {
                labels.push(colLabel);
            }
        });
        return labels;
    };

    const makeTableResponsive = (table) => {
        const spanOffsets = [];
        const setOffset = (row, col, span) => {
            spanOffsets[`${row}-${col}`] = span + (spanOffsets[`${row}-${col}`] ?? 0);
        };

        const labels = headerLabels(table.querySelectorAll('th'));
        table.querySelectorAll('tr').forEach((tr, trIndex) => {
            let rowOffset = 0;
            let colCount = labels.length;
            tr.querySelectorAll('td').forEach((td, tdIndex) => {
                rowOffset += (spanOffsets[`${trIndex}-${tdIndex}`] ?? 0);
                const rowIndex = tdIndex + rowOffset;

                const colSpan = parseInt(td.getAttribute('colspan') ?? '1');
                if(colSpan > 1)
                {
                    colSpan -= 1;
                    colCount -= colSpan;
                    for (let i = rowIndex + 1; i < colCount; i++) {
                        setOffset(trIndex, i, colSpan);
                    }
                }
                const rowSpan = parseInt(td.getAttribute('rowspan') ?? '1');
                if(rowSpan > 1)
                {
                    for (let i = 1; i < rowSpan; i++) {
                        setOffset(trIndex + i, rowIndex, 1);
                    }
                }

                td.setAttribute('data-label', labels[rowIndex]);
            });
        });
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
