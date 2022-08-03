try {
    if(typeof jaxon.config == undefined)
        jaxon.config = {};
}
catch(e) {
    jaxon = {};
    jaxon.config = {};
};

jaxon.config.requestURI = "/ajax";
jaxon.config.statusMessages = false;
jaxon.config.waitCursor = true;
jaxon.config.version = "Jaxon 4.0";
jaxon.config.defaultMode = "asynchronous";
jaxon.config.defaultMethod = "POST";
jaxon.config.responseType = "JSON";
metaTags = document.getElementsByTagName('meta');
for(let i = 0; i < metaTags.length; i++)
{
    if(metaTags[i].getAttribute('name') == 'csrf-token')
    {
        if((csrfToken = metaTags[i].getAttribute('content')))
        {
            jaxon.config.postHeaders = {'X-CSRF-TOKEN': csrfToken};
        }
        break;
    }
}

App = {};
App.Ajax = {};
App.Ajax.App = {};
App.Ajax.App.Meeting = {};
App.Ajax.App.Meeting.Charge = {};
App.Ajax.App.Meeting.Financial = {};
App.Ajax.App.Meeting.Mutual = {};
App.Ajax.App.Planning = {};
App.Ajax.App.Profile = {};
App.Ajax.App.Member = {};
App.Ajax.App.Member.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Member', jxnmthd: 'home' }, { parameters: arguments, bags: ["member"] });
};
App.Ajax.App.Member.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Member', jxnmthd: 'page' }, { parameters: arguments, bags: ["member"] });
};
App.Ajax.App.Member.number = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Member', jxnmthd: 'number' }, { parameters: arguments });
};
App.Ajax.App.Member.add = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Member', jxnmthd: 'add' }, { parameters: arguments, bags: ["faker"] });
};
App.Ajax.App.Member.fakes = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Member', jxnmthd: 'fakes' }, { parameters: arguments, bags: ["faker"] });
};
App.Ajax.App.Member.create = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Member', jxnmthd: 'create' }, { parameters: arguments });
};
App.Ajax.App.Member.edit = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Member', jxnmthd: 'edit' }, { parameters: arguments });
};
App.Ajax.App.Member.update = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Member', jxnmthd: 'update' }, { parameters: arguments });
};
App.Ajax.App.Charge = {};
App.Ajax.App.Charge.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Charge', jxnmthd: 'home' }, { parameters: arguments, bags: ["charge"] });
};
App.Ajax.App.Charge.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Charge', jxnmthd: 'page' }, { parameters: arguments, bags: ["charge"] });
};
App.Ajax.App.Charge.number = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Charge', jxnmthd: 'number' }, { parameters: arguments });
};
App.Ajax.App.Charge.add = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Charge', jxnmthd: 'add' }, { parameters: arguments, bags: ["faker"] });
};
App.Ajax.App.Charge.fakes = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Charge', jxnmthd: 'fakes' }, { parameters: arguments, bags: ["faker"] });
};
App.Ajax.App.Charge.create = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Charge', jxnmthd: 'create' }, { parameters: arguments });
};
App.Ajax.App.Charge.edit = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Charge', jxnmthd: 'edit' }, { parameters: arguments });
};
App.Ajax.App.Charge.update = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Charge', jxnmthd: 'update' }, { parameters: arguments, bags: ["charge"] });
};
App.Ajax.CallableClass = {};
App.Ajax.App.Meeting.Fund = {};
App.Ajax.App.Meeting.Fund.deposits = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Fund', jxnmthd: 'deposits' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Fund.remittances = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Fund', jxnmthd: 'remittances' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Table = {};
App.Ajax.App.Meeting.Table.select = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Table', jxnmthd: 'select' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Table.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Table', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Table.amounts = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Table', jxnmthd: 'amounts' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Table.deposits = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Table', jxnmthd: 'deposits' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Table.print = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Table', jxnmthd: 'print' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Profile.Round = {};
App.Ajax.App.Profile.Round.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Round', jxnmthd: 'home' }, { parameters: arguments, bags: ["tontine"] });
};
App.Ajax.App.Profile.Round.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Round', jxnmthd: 'page' }, { parameters: arguments, bags: ["tontine"] });
};
App.Ajax.App.Profile.Round.select = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Round', jxnmthd: 'select' }, { parameters: arguments, bags: ["tontine"] });
};
App.Ajax.App.Profile.Round.add = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Round', jxnmthd: 'add' }, { parameters: arguments, bags: ["tontine"] });
};
App.Ajax.App.Profile.Round.create = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Round', jxnmthd: 'create' }, { parameters: arguments, bags: ["tontine"] });
};
App.Ajax.App.Profile.Round.edit = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Round', jxnmthd: 'edit' }, { parameters: arguments, bags: ["tontine"] });
};
App.Ajax.App.Profile.Round.update = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Round', jxnmthd: 'update' }, { parameters: arguments, bags: ["tontine"] });
};
App.Ajax.App.Planning.Fund = {};
App.Ajax.App.Planning.Fund.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Fund', jxnmthd: 'home' }, { parameters: arguments, bags: ["fund","subscription"] });
};
App.Ajax.App.Planning.Fund.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Fund', jxnmthd: 'page' }, { parameters: arguments, bags: ["fund","subscription"] });
};
App.Ajax.App.Planning.Fund.number = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Fund', jxnmthd: 'number' }, { parameters: arguments });
};
App.Ajax.App.Planning.Fund.add = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Fund', jxnmthd: 'add' }, { parameters: arguments, bags: ["faker"] });
};
App.Ajax.App.Planning.Fund.fakes = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Fund', jxnmthd: 'fakes' }, { parameters: arguments, bags: ["faker"] });
};
App.Ajax.App.Planning.Fund.create = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Fund', jxnmthd: 'create' }, { parameters: arguments });
};
App.Ajax.App.Planning.Fund.edit = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Fund', jxnmthd: 'edit' }, { parameters: arguments });
};
App.Ajax.App.Planning.Fund.update = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Fund', jxnmthd: 'update' }, { parameters: arguments });
};
App.Ajax.App.Planning.Table = {};
App.Ajax.App.Planning.Table.select = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Table', jxnmthd: 'select' }, { parameters: arguments, bags: ["table"] });
};
App.Ajax.App.Planning.Table.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Table', jxnmthd: 'home' }, { parameters: arguments, bags: ["table"] });
};
App.Ajax.App.Planning.Table.amounts = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Table', jxnmthd: 'amounts' }, { parameters: arguments, bags: ["table"] });
};
App.Ajax.App.Planning.Table.deposits = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Table', jxnmthd: 'deposits' }, { parameters: arguments, bags: ["table"] });
};
App.Ajax.App.Planning.Table.toggleSession = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Table', jxnmthd: 'toggleSession' }, { parameters: arguments, bags: ["table"] });
};
App.Ajax.App.Planning.Table.remittances = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Table', jxnmthd: 'remittances' }, { parameters: arguments, bags: ["table"] });
};
App.Ajax.App.Planning.Table.saveBeneficiary = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Table', jxnmthd: 'saveBeneficiary' }, { parameters: arguments, bags: ["table"] });
};
App.Ajax.App.Meeting.Session = {};
App.Ajax.App.Meeting.Session.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Session', jxnmthd: 'home' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Meeting.Session.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Session', jxnmthd: 'page' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Profile.Tontine = {};
App.Ajax.App.Profile.Tontine.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Tontine', jxnmthd: 'home' }, { parameters: arguments, bags: ["tontine"] });
};
App.Ajax.App.Profile.Tontine.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Tontine', jxnmthd: 'page' }, { parameters: arguments, bags: ["tontine"] });
};
App.Ajax.App.Profile.Tontine.add = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Tontine', jxnmthd: 'add' }, { parameters: arguments });
};
App.Ajax.App.Profile.Tontine.create = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Tontine', jxnmthd: 'create' }, { parameters: arguments });
};
App.Ajax.App.Profile.Tontine.edit = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Tontine', jxnmthd: 'edit' }, { parameters: arguments });
};
App.Ajax.App.Profile.Tontine.update = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Profile.Tontine', jxnmthd: 'update' }, { parameters: arguments });
};
App.Ajax.App.Meeting.Deposit = {};
App.Ajax.App.Meeting.Deposit.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Deposit', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Deposit.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Deposit', jxnmthd: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Deposit.addDeposit = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Deposit', jxnmthd: 'addDeposit' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Deposit.delDeposit = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Deposit', jxnmthd: 'delDeposit' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Meeting = {};
App.Ajax.App.Meeting.Meeting.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Meeting', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Meeting.funds = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Meeting', jxnmthd: 'funds' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Meeting.bids = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Meeting', jxnmthd: 'bids' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Meeting.charges = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Meeting', jxnmthd: 'charges' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Meeting.summary = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Meeting', jxnmthd: 'summary' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Meeting.open = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Meeting', jxnmthd: 'open' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Meeting.close = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Meeting', jxnmthd: 'close' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Meeting.saveAgenda = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Meeting', jxnmthd: 'saveAgenda' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Meeting.saveReport = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Meeting', jxnmthd: 'saveReport' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Planning.Session = {};
App.Ajax.App.Planning.Session.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'home' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'page' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.number = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'number' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.add = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'add' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.years = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'years' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.copy = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'copy' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.create = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'create' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.edit = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'edit' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.update = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'update' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.editVenue = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'editVenue' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Planning.Session.saveVenue = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Session', jxnmthd: 'saveVenue' }, { parameters: arguments, bags: ["session"] });
};
App.Ajax.App.Meeting.Charge.Fee = {};
App.Ajax.App.Meeting.Charge.Fee.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Fee', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Fee.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Fee', jxnmthd: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Fine = {};
App.Ajax.App.Meeting.Charge.Fine.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Fine', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Fine.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Fine', jxnmthd: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Planning.Subscription = {};
App.Ajax.App.Planning.Subscription.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Subscription', jxnmthd: 'home' }, { parameters: arguments, bags: ["subscription"] });
};
App.Ajax.App.Planning.Subscription.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Subscription', jxnmthd: 'page' }, { parameters: arguments, bags: ["subscription"] });
};
App.Ajax.App.Planning.Subscription.filter = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Subscription', jxnmthd: 'filter' }, { parameters: arguments, bags: ["subscription"] });
};
App.Ajax.App.Planning.Subscription.create = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Subscription', jxnmthd: 'create' }, { parameters: arguments, bags: ["subscription"] });
};
App.Ajax.App.Planning.Subscription.delete = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Planning.Subscription', jxnmthd: 'delete' }, { parameters: arguments, bags: ["subscription"] });
};
App.Ajax.App.Meeting.Charge.Member = {};
App.Ajax.App.Meeting.Charge.Member.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Member', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Member.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Member', jxnmthd: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Member.toggleFilter = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Member', jxnmthd: 'toggleFilter' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Member.addFine = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Member', jxnmthd: 'addFine' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Member.delFine = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Member', jxnmthd: 'delFine' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Refund = {};
App.Ajax.App.Meeting.Financial.Refund.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Refund', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Refund.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Refund', jxnmthd: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Refund.toggleFilter = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Refund', jxnmthd: 'toggleFilter' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Refund.create = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Refund', jxnmthd: 'create' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Refund.delete = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Refund', jxnmthd: 'delete' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Settlement = {};
App.Ajax.App.Meeting.Charge.Settlement.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Settlement', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Settlement.page = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Settlement', jxnmthd: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Settlement.toggleFilter = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Settlement', jxnmthd: 'toggleFilter' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Settlement.addSettlement = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Settlement', jxnmthd: 'addSettlement' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Charge.Settlement.delSettlement = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Charge.Settlement', jxnmthd: 'delSettlement' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Bidding = {};
App.Ajax.App.Meeting.Financial.Bidding.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Bidding', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Bidding.addBidding = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Bidding', jxnmthd: 'addBidding' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Bidding.saveBidding = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Bidding', jxnmthd: 'saveBidding' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Bidding.deleteBidding = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Bidding', jxnmthd: 'deleteBidding' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Mutual.Remittance = {};
App.Ajax.App.Meeting.Mutual.Remittance.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Mutual.Remittance', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Mutual.Remittance.addRemittance = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Mutual.Remittance', jxnmthd: 'addRemittance' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Mutual.Remittance.delRemittance = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Mutual.Remittance', jxnmthd: 'delRemittance' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Remittance = {};
App.Ajax.App.Meeting.Financial.Remittance.home = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Remittance', jxnmthd: 'home' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Remittance.addRemittance = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Remittance', jxnmthd: 'addRemittance' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Remittance.saveRemittance = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Remittance', jxnmthd: 'saveRemittance' }, { parameters: arguments, bags: ["meeting"] });
};
App.Ajax.App.Meeting.Financial.Remittance.deleteRemittance = function() {
    return jaxon.request({ jxncls: 'App.Ajax.App.Meeting.Financial.Remittance', jxnmthd: 'deleteRemittance' }, { parameters: arguments, bags: ["meeting"] });
};

jaxon.dialogs = {};
/*
 * Bootbox dialogs plugin
 */
jaxon.dialogs.bootbox = {
    alert: function(type, content, title) {
        var html = '<div class="alert alert-' + type + '" style="margin-top:15px;margin-bottom:-15px;">';
        if(title != undefined && title != '')
            html += '<strong>' + title + '</strong><br/>';
        html += content + '</div>';
        bootbox.alert(html);
    },
    success: function(content, title) {
        jaxon.dialogs.bootbox.alert('success', content, title);
    },
    info: function(content, title) {
        jaxon.dialogs.bootbox.alert('info', content, title);
    },
    warning: function(content, title) {
        jaxon.dialogs.bootbox.alert('warning', content, title);
    },
    error: function(content, title) {
        jaxon.dialogs.bootbox.alert('danger', content, title);
    },
    confirm: function(question, title, yesCallback, noCallback) {
        bootbox.confirm({
            title: title,
            message: question,
            buttons: {
                cancel: {label: "No"},
                confirm: {label: "Yes"}
            },
            callback: function(res){
                if(res)
                    yesCallback();
                else if(typeof noCallback == 'function')
                    noCallback();
            }
        });
    }
};

/*
 * Bootstrap dialogs plugin
 */
jaxon.dialogs.bootstrap = {
    show: function(args) {
        // Open modal
        BootstrapDialog.show({
            ...args.data,
            buttons: args.data.buttons.map(button => {
                return {
                    ...button,
                    action: button.action === 'close' ? function(dialog){dialog.close();} : new Function(button.action),
                };
            }),
        });
    },
    hide: function(args) {
        // Hide modal
        BootstrapDialog.closeAll();
    },
    alert: function(args) {
        var dataTypes = {
            success: BootstrapDialog.TYPE_SUCCESS,
            info: BootstrapDialog.TYPE_INFO,
            warning: BootstrapDialog.TYPE_WARNING,
            danger: BootstrapDialog.TYPE_DANGER
        };
        args.data.type = dataTypes[args.data.type];
        BootstrapDialog.alert(args.data);
    },
    success: function(content, title) {
        BootstrapDialog.alert({type: BootstrapDialog.TYPE_SUCCESS, message: content, title: title});
    },
    info: function(content, title) {
        BootstrapDialog.alert({type: BootstrapDialog.TYPE_INFO, message: content, title: title});
    },
    warning: function(content, title) {
        BootstrapDialog.alert({type: BootstrapDialog.TYPE_WARNING, message: content, title: title});
    },
    error: function(content, title) {
        BootstrapDialog.alert({type: BootstrapDialog.TYPE_DANGER, message: content, title: title});
    },
    confirm: function(question, title, yesCallback, noCallback) {
        BootstrapDialog.confirm({
            title: title,
            message: question,
            btnOKLabel: "Yes",
            btnCancelLabel: "No",
            callback: function(res){
                if(res)
                    yesCallback();
                else if(noCallback != undefined)
                    noCallback();
            }
        });
    }
};

jaxon.dialogs.pgwjs = {
    defaults: {maxWidth: 400},
    options: {},
    show: function(args) {
        // Set user and default options into data only when they are missing
        for(key in jaxon.dialogs.pgwjs.options)
        {
            if(!(key in args.data))
            {
                args.data[key] = jaxon.dialogs.pgwjs.options[key];
            }
        }
        for(key in jaxon.dialogs.pgwjs.defaults)
        {
            if(!(key in args.data))
            {
                args.data[key] = jaxon.dialogs.pgwjs.defaults[key];
            }
        }
        $.pgwModal(args.data);
    }
};

jaxon.dialogs.toastr = {
    success: function(content, title) {
        if((title))
            toastr.success(content, title);
        else
            toastr.success(content);
    },
    info: function(content, title) {
        if((title))
            toastr.info(content, title);
        else
            toastr.info(content);
    },
    warning: function(content, title) {
        if((title))
            toastr.warning(content, title);
        else
            toastr.warning(content);
    },
    error: function(content, title) {
        if((title))
            toastr.error(content, title);
        else
            toastr.error(content);
    }
};

jaxon.dialogs.cutealert = {
    success: function(content, title = 'Success') {
        $.jAlert({content: content, title: title, theme: 'green'});
    },
    info: function(content, title = 'Information') {
        $.jAlert({content: content, title: title, theme: 'blue'});
    },
    warning: function(content, title = 'Warning') {
        $.jAlert({content: content, title: title, theme: 'yellow'});
    },
    error: function(content, title = 'Error') {
        $.jAlert({content: content, title: title, theme: 'red'});
    },
    confirm: function(question, title, yesCallback, noCallback) {
        if(noCallback === undefined)
            noCallback = function(){};
        $.jAlert({
            title: title,
            type: "confirm",
            confirmQuestion: question,
            confirmBtnText: "Yes",
            denyBtnText: "No",
            onConfirm: yesCallback,
            onDeny: noCallback
        });
    }
};

jaxon.dialogs.tingle = {
    modal: null,
    show: function(args) {
        if(jaxon.dialogs.tingle.modal != null)
        {
            jaxon.dialogs.tingle.modal.close();
        }
        jaxon.dialogs.tingle.modal = new tingle.modal(args.data.options);
        // Set content
        jaxon.dialogs.tingle.modal.setContent(args.data.content);
        // Add buttons
        for(var ind = 0, len = args.data.buttons.length; ind < len; ind++)
        {
            button = args.data.buttons[ind];
            if(button.click == "close")
            {
                button.click = function(){jaxon.dialogs.tingle.modal.close();};
            }
            else
            {
                button.click = new Function(button.click);
            }
            jaxon.dialogs.tingle.modal.addFooterBtn(button.title, button.class, button.click);
        }
        // Open modal
        jaxon.dialogs.tingle.modal.open();
    },
    hide: function(args) {
        if(jaxon.dialogs.tingle.modal != null)
        {
            // Close an destroy modal
            jaxon.dialogs.tingle.modal.close();
            jaxon.dialogs.tingle.modal.destroy();
            jaxon.dialogs.tingle.modal = null;
        }
    }
};

jaxon.dialogs.noty = {
    success: function(content, title) {
        noty({text: content, type: 'success', layout: 'topCenter', timeout: 5000});
    },
    info: function(content, title) {
        noty({text: content, type: 'information', layout: 'topCenter', timeout: 5000});
    },
    warning: function(content, title) {
        noty({text: content, type: 'warning', layout: 'topCenter', timeout: 5000});
    },
    error: function(content, title) {
        noty({text: content, type: 'error', layout: 'topCenter', timeout: 5000});
    },
    confirm: function(question, title, yesCallback, noCallback) {
        noty({
            text: question,
            layout: 'topCenter',
            buttons: [
                {
                    addClass: 'btn btn-primary',
                    text: "Yes",
                    onClick: function($noty){
                        $noty.close();
                        yesCallback();
                    }
                },{
                    addClass: 'btn btn-danger',
                    text: "No",
                    onClick: function($noty){
                        $noty.close();
                        if(noCallback !== undefined)
                            noCallback();
                    }
                }
            ]
        });
    }
};

jaxon.dialogs.notify = {
    success: function(content, title) {
        $.notify(content, {className: 'success', position: "top center"});
    },
    info: function(content, title) {
        $.notify(content, {className: 'info', position: "top center"});
    },
    warning: function(content, title) {
        $.notify(content, {className: 'warn', position: "top center"});
    },
    error: function(content, title) {
        $.notify(content, {className: 'error', position: "top center"});
    }
};

jaxon.dialogs.overhang = {
    success: function(content, title) {
        $("body").overhang({message: content, title: title, type: 'success', duration: 5});
    },
    info: function(content, title) {
        $("body").overhang({message: content, title: title, type: 'info', duration: 5});
    },
    warning: function(content, title) {
        $("body").overhang({message: content, title: title, type: 'warn', duration: 5});
    },
    error: function(content, title) {
        $("body").overhang({message: content, title: title, type: 'error', duration: 5});
    },
    confirm: function(question, title, yesCallback, noCallback) {
        $("body").overhang({
            type: "confirm",
            message: question,
            callback: function(res){
                if(res)
                    yesCallback();
                else if(noCallback != undefined)
                    noCallback();
            }
        });
    }
};

jaxon.dialogs.pnotify = {
    alert: function(data) {
        notice = new PNotify(data);
        notice.get().click(function(){notice.remove();});
    },
    success: function(content, title) {
        jaxon.dialogs.pnotify.alert({text: content, title: title, type: 'success'});
    },
    info: function(content, title) {
        jaxon.dialogs.pnotify.alert({text: content, title: title, type: 'info'});
    },
    warning: function(content, title) {
        jaxon.dialogs.pnotify.alert({text: content, title: title, type: 'notice'});
    },
    error: function(content, title) {
        jaxon.dialogs.pnotify.alert({text: content, title: title, type: 'error'});
    },
    confirm: function(question, title, yesCallback, noCallback) {
        PNotify.prototype.options.confirm.buttons[0].text = "Yes";
        PNotify.prototype.options.confirm.buttons[1].text = "No";
        notice = new PNotify({
            title: title,
            text: question,
            hide: false,
            confirm:{
                confirm: true
            },
            buttons:{
                closer: false,
                sticker: false,
                labels: {

                }
            }
        });
        notice.get().on("pnotify.confirm", yesCallback);
        if(noCallback != undefined)
            notice.get().on("pnotify.cancel", noCallback);
    }
};

jaxon.dialogs.swal = {
    options: {
        allowEscapeKey: true,
        allowOutsideClick: true
    },
    success: function(content, title) {
        if(title == undefined) title = 'Success';
        swal({text: content, title: title, type: 'success'});
    },
    info: function(content, title) {
        if(title == undefined) title = 'Information';
        swal({text: content, title: title, type: 'info'});
    },
    warning: function(content, title) {
        if(title == undefined) title = 'Warning';
        swal({text: content, title: title, type: 'warning'});
    },
    error: function(content, title) {
        if(title == undefined) title = 'Error';
        swal({text: content, title: title, type: 'error'});
    },
    confirm: function(question, title, yesCallback, noCallback) {
        swal({
            type: "warning",
            title: title,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            showCancelButton: true,
            text: question
        },
        function(res){
            if(res)
                yesCallback();
            else if(noCallback != undefined)
                noCallback();
        });
    }
};

jaxon.dialogs.jconfirm = {
    dialog: null,
    show: function(args) {
        // Add buttons
        for(key in args.data.buttons)
        {
            button = args.data.buttons[key];
            if(button.action === 'close')
            {
                button.action = function(){jaxon.dialogs.jconfirm.dialog.close();};
            }
            else
            {
                button.action = new Function(button.action);
            }
        }
        args.data.closeIcon = true;
        if((jaxon.dialogs.jconfirm.dialog))
        {
            jaxon.dialogs.jconfirm.dialog.close();
        }
        jaxon.dialogs.jconfirm.dialog = $.confirm(args.data);
    },
    hide: function(args) {
        if((jaxon.dialogs.jconfirm.dialog))
        {
            jaxon.dialogs.jconfirm.dialog.close();
        }
        jaxon.dialogs.jconfirm.dialog = null;
    },
    success: function(content, title) {
        $.alert({content: content, title: title, type: 'green', icon: 'fa fa-success'});
    },
    info: function(content, title) {
        $.alert({content: content, title: title, type: 'blue', icon: 'fa fa-info'});
    },
    warning: function(content, title) {
        $.alert({content: content, title: title, type: 'orange', icon: 'fa fa-warning'});
    },
    error: function(content, title) {
        $.alert({content: content, title: title, type: 'red', icon: 'fa fa-error'});
    },
    confirm: function(question, title, yesCallback, noCallback) {
        if(noCallback == undefined)
            noCallback = function(){};
        $.confirm({
            title: title,
            content: question,
            buttons: {
                yes: {
                    btnClass: "btn-blue",
                    text: "Yes",
                    action: yesCallback
                },
                no: {
                    text: "No",
                    action: noCallback
                }
            }
        });
    }
};

jaxon.dialogs.xdialog = {
    show: function(args) {
        jaxon.dialogs.xdialog.dialog = xdialog.open({
            ...args.data,
            onok: args.data.onok ? new Function(args.data.onok) : null,
            oncancel: args.data.oncancel ? new Function(args.data.oncancel) : null,
        });
    },
    hide: function() {
        // Hide modal
        jaxon.dialogs.xdialog.dialog.close();
    },
    success: function(body, title = 'Success') {
        xdialog.alert(body, {title});
    },
    info: function(body, title = 'Information') {
        xdialog.info(body, {title});
    },
    warning: function(body, title = 'Warning') {
        xdialog.warn(body, {title});
    },
    error: function(body, title = 'Error') {
        xdialog.error(body, {title});
    },
    confirm: function(question, title, yesCallback, noCallback) {
        xdialog.confirm(question, yesCallback, {
            title,
            buttons: {
                ok: "Yes",
                cancel: "No",
            },
            oncancel: noCallback,
        });
    }
};

jaxon.dialogs.cutealert = {
    success: function(content, title = 'Success') {
        cuteAlert({message: content, title: title, type: 'green'});
    },
    info: function(content, title = 'Information') {
        cuteAlert({message: content, title: title, type: 'blue'});
    },
    warning: function(content, title = 'Warning') {
        cuteAlert({message: content, title: title, type: 'yellow'});
    },
    error: function(content, title = 'Error') {
        cuteAlert({message: content, title: title, type: 'red'});
    },
    confirm: function(question, title, yesCallback, noCallback) {
        cuteAlert({
            title: title,
            type: 'question',
            message: question,
            confirmText: "Yes",
            cancelText: "No",
        }).then(e => {
            if(e === 'confirm')
            {
                yesCallback();
            }
            else if((noCallback))
            {
                noCallback();
            }
        });
    }
};

jaxon.dom.ready(function() {
jaxon.command.handler.register("jquery", function(args) {
        jaxon.cmd.script.execute(args);
    });

jaxon.command.handler.register("bags.set", function(args) {
        for (const bag in args.data) {
            jaxon.ajax.parameters.bags[bag] = args.data[bag];
        }
    });

/*
 * Bootbox dialogs plugin
 */
    if(!$('#bootbox-container').length)
    {
        $('body').append('<div id="bootbox-container"></div>');
    }
    jaxon.command.handler.register("bootbox", function(args) {
        bootbox.alert(args.data.type, args.data.content, args.data.title);
    });

/*
 * Bootstrap dialogs plugin
 */
    jaxon.command.handler.register("bootstrap.show", jaxon.dialogs.bootstrap.show);
    jaxon.command.handler.register("bootstrap.hide", jaxon.dialogs.bootstrap.hide);
    jaxon.command.handler.register("bootstrap.alert", jaxon.dialogs.bootstrap.alert);

jaxon.command.handler.register("pgw.modal", jaxon.dialogs.pgwjs.show);

toastr.options.closeButton = true;
    toastr.options.closeDuration = 0;
    toastr.options.positionClass = 'toast-top-center';    jaxon.ajax.message.success = jaxon.dialogs.toastr.success;
    jaxon.ajax.message.info = jaxon.dialogs.toastr.info;
    jaxon.ajax.message.warning = jaxon.dialogs.toastr.warning;
    jaxon.ajax.message.error = jaxon.dialogs.toastr.error;
    jaxon.command.handler.register("toastr.info", function(args) {
        jaxon.dialogs.toastr.info(args.data.message, args.data.title);
    });
    jaxon.command.handler.register("toastr.success", function(args) {
        jaxon.dialogs.toastr.success(args.data.message, args.data.title);
    });
    jaxon.command.handler.register("toastr.warning", function(args) {
        jaxon.dialogs.toastr.warning(args.data.message, args.data.title);
    });
    jaxon.command.handler.register("toastr.error", function(args) {
        jaxon.dialogs.toastr.error(args.data.message, args.data.title);
    });

jaxon.command.handler.register("jalert.alert", function(args) {
        $.jAlert(args.data);
    });

jaxon.command.handler.register("tingle.show", jaxon.dialogs.tingle.show);
    jaxon.command.handler.register("tingle.hide", jaxon.dialogs.tingle.hide);

jaxon.ajax.message.confirm = jaxon.dialogs.noty.confirm;
    jaxon.command.handler.register('noty.alert', function(args) {
        noty({text: args.data.text, type: args.data.type, layout: 'topCenter', timeout: 5000});
    });

jaxon.command.handler.register("notify.alert", function(args) {
        $.notify(args.data.message, {className: args.data.className, position: "top center"});
    });

jaxon.command.handler.register("overhang.alert", function(args) {
        // Default options
        args.data.duration = 5;
        $("body").overhang(args.data);
    });

PNotify.prototype.options.delay = 5000;
    PNotify.prototype.options.styling = 'fontawesome';
    jaxon.command.handler.register("pnotify.alert", function(args) {
        jaxon.pnotify.alert(args.data);
    });

jaxon.command.handler.register("sweetalert.alert", function(args) {
        // Set user and default options into data only when they are missing
        for(key in jaxon.dialogs.swal.options)
        {
            if(!(key in args.data))
            {
                args.data[key] = jaxon.dialogs.swal.options[key];
            }
        }
        swal(args.data);
    });

jaxon.command.handler.register("jconfirm.show", jaxon.dialogs.jconfirm.show);
    jaxon.command.handler.register("jconfirm.hide", jaxon.dialogs.jconfirm.hide);
    jaxon.command.handler.register("jconfirm.alert", function(args) {
        $.alert(args.data);
    });

jaxon.command.handler.register("xdialog.show", jaxon.dialogs.xdialog.show);
    jaxon.command.handler.register("xdialog.hide", jaxon.dialogs.xdialog.hide);
    jaxon.command.handler.register("xdialog.success", function(args) {
        jaxon.dialogs.xdialog.success(args.data.body, args.data.title);
    });
    jaxon.command.handler.register("xdialog.info", function(args) {
        jaxon.dialogs.xdialog.info(args.data.body, args.data.title);
    });
    jaxon.command.handler.register("xdialog.warning", function(args) {
        jaxon.dialogs.xdialog.warning(args.data.body, args.data.title);
    });
    jaxon.command.handler.register("xdialog.error", function(args) {
        jaxon.dialogs.xdialog.error(args.data.body, args.data.title);
    });

jaxon.command.handler.register("cutealert.alert", function(args) {
        cuteAlert(args.data);
    });
});
