jaxon.dom.ready(function() {
    tontine.createSpinner();
    // The function must be called after the callbacks are defined.
    tontine.home();
});

var tontine = {};
(function(self) {
    const spinner = {
        exec: null,
        count: 0, // To make sure that the spinner is started once.
    };

    // Create the spinner.
    self.createSpinner = () => spinner.exec = new Spin.Spinner({ position: 'fixed' });

    // Callback to show our custom spinner.
    self.spin = {
        onRequest: function() {
            if(spinner.count++ === 0)
            {
                spinner.exec.spin(document.body);
            }
        },
        onComplete: function() {
            if(--spinner.count === 0)
            {
                spinner.exec.stop();
            }
        },
        onFailure: function() {
            if(--spinner.count === 0)
            {
                spinner.exec.stop();
            }
        },
    };

    // Callback to empty the currency list while fetching the new currencies.
    self.currency = {
        onRequest: () => $('#select_currency_container select').html(''),
    };

    // Callback to hide the sidebar menu on mobile devices
    self.hideMenu = {
        onRequest: () => $('body').trigger('touchend'),
    };

    self.setLoanInterestLabel = () => {
        $('#loan-interest-type').change(() => {
            const type = $('#loan-interest-type').val();
            $('#loan-interest-label').html(type === 'f' ?
                self.labels.amount : self.labels.percentage);
        });
    };

    // Convert HTML code to text.
    const convertHtmlToText = (html) => {
        const element = document.createElement("div");
        element.innerHTML = html;
        const text = element.textContent || element.innerText || "";
        return text.trim()/*.replace("\n", " ")*/;
    };

    // Get the labels for tables cells, taking the colspan attr into account.
    const headerLabels = (tableHeaders) => {
        const labels = [];
        Array.from(tableHeaders).forEach((header) => {
            const colSpan = parseInt(header.getAttribute('colspan') ?? '1');
            // Convert the label to text using jQuery.
            const label = convertHtmlToText(`<span>${header.innerHTML}</span>`);
            // The label can be duplicated depending on the colspan attribute.
            for (let i = 0; i < colSpan; i++) {
                labels.push(label);
            }
        });
        return labels;
    };

    // Add labels to table cells.
    const makeTableResponsive = (table) => {
        // Due to colspan and rowspan attributes, some offset might need to be applied to labels.
        const spanOffsets = [];
        const setOffset = (row, col, span) => {
            const offsetKey = `${row}-${col}`;
            spanOffsets[offsetKey] = span + (spanOffsets[offsetKey] ?? 0);
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

                // Don't overwrite existing labels.
                if (!td.hasAttribute('data-label')) {
                    td.setAttribute('data-label', labels[rowIndex]);
                }
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

    /**
     * Create the select dropdown for a subscription
     *
     * @param {int} beneficiaryId 
     * @param {array} candidates 
     * @returns {string}
     */
    const getSubscriptionSelect = (beneficiaryId, candidates) => `
<select class="form-control my-2 select-beneficiary" style="height:36px; padding:5px 5px;">`
    + candidates.reduce((options, { id, name }) => options + `
    <option value="${id}"` + (id !== 0 && id === beneficiaryId ?
        ' selected="selected"' : '') + `>${name}</option>`, '') + `
</select>`;

    /**
     * @param {array} candidates 
     * @param {array} beneficiaries
     * @returns {void}
     */
    self.setSubscriptionCandidates = (candidates, beneficiaries) => {
        const wrapper = $('#content-subscription-beneficiaries');
        $('.session-subscription-candidate', wrapper)
            .html(getSubscriptionSelect(0, candidates));

        beneficiaries.forEach(({ id, name }) => {
            $(`#session-subscription-candidate-${id}`, wrapper)
                .html(getSubscriptionSelect(id, [ ...candidates, { id, name } ]));
        });
    };
})(tontine);
