jaxon.dom.ready(function() {
    $('#tontine-menu-tontines').click(tontine.home);
    $('#tontine-menu-users').css('color', '#6777ef');
    $('#tontine-menu-users').click(tontine.users);
    tontine.home();

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
                tontine.labels.amount : tontine.labels.percentage);
        });
    }

    self.makeTableResponsive = (tableId) => {
        const table = document.querySelector('#' + tableId);
        if(!table) {
            return;
        }
        const labels = Array.from(table.querySelectorAll('th')).map(th => th.innerText);
        table.querySelectorAll('td')
            .forEach((td, i) => td.setAttribute('data-label', labels[i % labels.length]));
    }

    self.showSmScreen = (targetId, wrapperId) => {
        $('.sm-screen', $('#' + wrapperId)).removeClass('sm-screen-active');
        $('#' + targetId).addClass('sm-screen-active');
    }

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
    }
})(Tontine);
