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

    // Check if it is a mobile device
    if('ontouchstart' in document.documentElement)
    {
        // Lock screen in landscape mode
        // screen.orientation.lock('landscape');
        jaxon.ajax.message.info(tontine.messages.orientation, tontine.titles.message);
    }
});

function setLoanInterestLabel()
{
    $('#loan-interest-type').change(() => {
        const type = $('#loan-interest-type').val();
        $('#loan-interest-label').html(type === 'f' ?
            tontine.labels.amount : tontine.labels.percentage);
    });
}

function showPaymentMembers()
{
    $('#payment-members-home').removeClass('payment-sm-hide');
    $('#payment-payables-home').removeClass('payment-sm-hide');
    $('#payment-payables-home').addClass('payment-sm-hide');
}
function showPaymentDetails()
{
    $('#payment-members-home').removeClass('payment-sm-hide');
    $('#payment-payables-home').removeClass('payment-sm-hide');
    $('#payment-members-home').addClass('payment-sm-hide');
}
