jaxon.config.requestURI = "/ajax";
jaxon.config.statusMessages = false;
jaxon.config.waitCursor = true;
jaxon.config.version = "Jaxon 5.x";
jaxon.config.defaultMode = "asynchronous";
jaxon.config.defaultMethod = "POST";
jaxon.config.responseType = "JSON";



jaxon.setCsrf('csrf-token');

Ajax = {};
Ajax.App = {};
Ajax.App.Admin = {};
Ajax.App.Admin.Organisation = {};
Ajax.App.Admin.User = {};
Ajax.App.Admin.User.Guest = {};
Ajax.App.Admin.User.Host = {};
Ajax.App.Meeting = {};
Ajax.App.Meeting.Payment = {};
Ajax.App.Meeting.Presence = {};
Ajax.App.Meeting.Session = {};
Ajax.App.Meeting.Session.Cash = {};
Ajax.App.Meeting.Session.Charge = {};
Ajax.App.Meeting.Session.Charge.Fixed = {};
Ajax.App.Meeting.Session.Charge.Libre = {};
Ajax.App.Meeting.Session.Charge.Settlement = {};
Ajax.App.Meeting.Session.Credit = {};
Ajax.App.Meeting.Session.Credit.Partial = {};
Ajax.App.Meeting.Session.Pool = {};
Ajax.App.Meeting.Session.Pool.Deposit = {};
Ajax.App.Meeting.Session.Pool.Remitment = {};
Ajax.App.Meeting.Session.Saving = {};
Ajax.App.Meeting.Summary = {};
Ajax.App.Meeting.Summary.Cash = {};
Ajax.App.Meeting.Summary.Charge = {};
Ajax.App.Meeting.Summary.Credit = {};
Ajax.App.Meeting.Summary.Pool = {};
Ajax.App.Meeting.Summary.Saving = {};
Ajax.App.Page = {};
Ajax.App.Page.Sidebar = {};
Ajax.App.Planning = {};
Ajax.App.Planning.Pool = {};
Ajax.App.Planning.Pool.Session = {};
Ajax.App.Planning.Session = {};
Ajax.App.Planning.Subscription = {};
Ajax.App.Report = {};
Ajax.App.Report.Round = {};
Ajax.App.Report.Session = {};
Ajax.App.Report.Session.Action = {};
Ajax.App.Report.Session.Bill = {};
Ajax.App.Report.Session.Saving = {};
Ajax.App.Tontine = {};
Ajax.App.Tontine.Member = {};
Ajax.App.Tontine.Options = {};
if(Ajax.App.Faker === undefined) {
    Ajax.App.Faker = {};
}
Ajax.App.Faker.members = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Faker', method: 'members' }, { parameters: arguments, bags: ["tenant","faker"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Faker.charges = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Faker', method: 'charges' }, { parameters: arguments, bags: ["tenant","faker"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Faker.pools = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Faker', method: 'pools' }, { parameters: arguments, bags: ["tenant","faker"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Locale === undefined) {
    Ajax.App.Locale = {};
}
Ajax.App.Locale.selectCurrency = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Locale', method: 'selectCurrency' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.selectCurrency });
};
if(Ajax.App.Tontine.Select === undefined) {
    Ajax.App.Tontine.Select = {};
}
Ajax.App.Tontine.Select.showOrganisations = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Select', method: 'showOrganisations' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Select.saveOrganisation = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Select', method: 'saveOrganisation' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Select.showRounds = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Select', method: 'showRounds' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Select._saveRound = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Select', method: '_saveRound' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Select.saveRound = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Select', method: 'saveRound' }, { parameters: arguments, bags: ["tenant","tontine","planning"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Admin.User.User === undefined) {
    Ajax.App.Admin.User.User = {};
}
Ajax.App.Admin.User.User.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.User', method: 'home' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.User.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.User', method: 'render' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.User.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.User', method: 'clear' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.User.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.User', method: 'visible' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Round.Pool === undefined) {
    Ajax.App.Report.Round.Pool = {};
}
Ajax.App.Report.Round.Pool.refresh = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Pool', method: 'refresh' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Round.Pool.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Pool', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Round.Pool.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Pool', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Round.Pool.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Pool', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Pool.Pool === undefined) {
    Ajax.App.Planning.Pool.Pool = {};
}
Ajax.App.Planning.Pool.Pool.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'home' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.showIntro = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'showIntro' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.showDepositFixed = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'showDepositFixed' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.saveDepositFixed = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'saveDepositFixed' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.showDepositLendable = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'showDepositLendable' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.saveDepositLendable = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'saveDepositLendable' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.showRemitPlanned = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'showRemitPlanned' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.saveRemitPlanned = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'saveRemitPlanned' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.showRemitAuction = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'showRemitAuction' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.saveRemitAuction = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'saveRemitAuction' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'add' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'create' }, { parameters: arguments, bags: ["tenant","pool","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'edit' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'update' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'delete' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'render' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'clear' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Pool.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Pool', method: 'visible' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Round.Round === undefined) {
    Ajax.App.Report.Round.Round = {};
}
Ajax.App.Report.Round.Round.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Round', method: 'home' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Round.Round.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Round', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Round.Round.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Round', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Round.Round.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Round', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Loan === undefined) {
    Ajax.App.Report.Session.Loan = {};
}
Ajax.App.Report.Session.Loan.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Loan', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Loan.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Loan', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Loan.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Loan', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Admin.User.Host.Host === undefined) {
    Ajax.App.Admin.User.Host.Host = {};
}
Ajax.App.Admin.User.Host.Host.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Host', method: 'add' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Host.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Host', method: 'create' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Host.cancel = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Host', method: 'cancel' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Host.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Host', method: 'delete' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Host.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Host', method: 'render' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Host.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Host', method: 'clear' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Host.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Host', method: 'visible' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Round.Balance === undefined) {
    Ajax.App.Report.Round.Balance = {};
}
Ajax.App.Report.Round.Balance.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Balance', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Round.Balance.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Balance', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Round.Balance.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Round.Balance', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Tontine.Options.Fund === undefined) {
    Ajax.App.Tontine.Options.Fund = {};
}
Ajax.App.Tontine.Options.Fund.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Fund', method: 'add' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Fund.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Fund', method: 'create' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Fund.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Fund', method: 'edit' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Fund.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Fund', method: 'update' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Fund.toggle = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Fund', method: 'toggle' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Fund.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Fund', method: 'render' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Fund.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Fund', method: 'clear' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Fund.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Fund', method: 'visible' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Refund === undefined) {
    Ajax.App.Report.Session.Refund = {};
}
Ajax.App.Report.Session.Refund.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Refund', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Refund.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Refund', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Refund.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Refund', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Saving === undefined) {
    Ajax.App.Report.Session.Saving = {};
}
Ajax.App.Report.Session.Saving.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Saving', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Saving.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Saving', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Saving.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Saving', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Tontine.Member.Member === undefined) {
    Ajax.App.Tontine.Member.Member = {};
}
Ajax.App.Tontine.Member.Member.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'home' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.search = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'search' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'add' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'create' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.addList = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'addList' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.createList = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'createList' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'edit' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'update' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.toggle = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'toggle' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'delete' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'render' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'clear' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Member.Member.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.Member', method: 'visible' }, { parameters: arguments, bags: ["tenant","member"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Admin.User.Guest.Guest === undefined) {
    Ajax.App.Admin.User.Guest.Guest = {};
}
Ajax.App.Admin.User.Guest.Guest.accept = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.Guest', method: 'accept' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Guest.Guest.refuse = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.Guest', method: 'refuse' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Guest.Guest.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.Guest', method: 'delete' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Guest.Guest.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.Guest', method: 'render' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Guest.Guest.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.Guest', method: 'clear' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Guest.Guest.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.Guest', method: 'visible' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Admin.User.Host.Access === undefined) {
    Ajax.App.Admin.User.Host.Access = {};
}
Ajax.App.Admin.User.Host.Access.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Access', method: 'home' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Access.tontine = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Access', method: 'tontine' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Access.saveAccess = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Access', method: 'saveAccess' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Access.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Access', method: 'render' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Access.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Access', method: 'clear' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Host.Access.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.Access', method: 'visible' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Pool.PoolPage === undefined) {
    Ajax.App.Planning.Pool.PoolPage = {};
}
Ajax.App.Planning.Pool.PoolPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.PoolPage', method: 'page' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Pool.PoolPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.PoolPage', method: 'render' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Pool.PoolPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.PoolPage', method: 'clear' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Pool.PoolPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.PoolPage', method: 'visible' }, { parameters: arguments, bags: ["pool"] });
};
if(Ajax.App.Planning.Session.Round === undefined) {
    Ajax.App.Planning.Session.Round = {};
}
Ajax.App.Planning.Session.Round.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Round', method: 'home' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Round.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Round', method: 'add' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Round.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Round', method: 'create' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Round.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Round', method: 'edit' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Round.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Round', method: 'update' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Round.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Round', method: 'delete' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Round.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Round', method: 'render' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Round.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Round', method: 'clear' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Round.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Round', method: 'visible' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Deposit === undefined) {
    Ajax.App.Report.Session.Deposit = {};
}
Ajax.App.Report.Session.Deposit.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Deposit', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Deposit.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Deposit', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Deposit.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Deposit', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Session === undefined) {
    Ajax.App.Report.Session.Session = {};
}
Ajax.App.Report.Session.Session.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Session', method: 'home' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Session.showSession = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Session', method: 'showSession' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Session.showMember = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Session', method: 'showMember' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Session.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Session', method: 'render' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Session.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Session', method: 'clear' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Session.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Session', method: 'visible' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Tontine.Options.Charge === undefined) {
    Ajax.App.Tontine.Options.Charge = {};
}
Ajax.App.Tontine.Options.Charge.select = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'select' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Charge.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'add' }, { parameters: arguments, bags: ["tenant","charge","faker"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Charge.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'create' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Charge.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'edit' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Charge.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'update' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Charge.toggle = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'toggle' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Charge.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'delete' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Charge.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'render' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Charge.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'clear' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Charge.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Charge', method: 'visible' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Payment.Payable === undefined) {
    Ajax.App.Meeting.Payment.Payable = {};
}
Ajax.App.Meeting.Payment.Payable.show = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.Payable', method: 'show' }, { parameters: arguments, bags: ["tenant","payment"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Payment.Payable.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.Payable', method: 'render' }, { parameters: arguments, bags: ["tenant","payment"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Payment.Payable.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.Payable', method: 'clear' }, { parameters: arguments, bags: ["tenant","payment"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Payment.Payable.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.Payable', method: 'visible' }, { parameters: arguments, bags: ["tenant","payment"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Payment.Payment === undefined) {
    Ajax.App.Meeting.Payment.Payment = {};
}
Ajax.App.Meeting.Payment.Payment.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.Payment', method: 'home' }, { parameters: arguments, bags: ["tenant","payment"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Payment.Payment.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.Payment', method: 'render' }, { parameters: arguments, bags: ["tenant","payment"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Payment.Payment.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.Payment', method: 'clear' }, { parameters: arguments, bags: ["tenant","payment"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Payment.Payment.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.Payment', method: 'visible' }, { parameters: arguments, bags: ["tenant","payment"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Presence.Member === undefined) {
    Ajax.App.Meeting.Presence.Member = {};
}
Ajax.App.Meeting.Presence.Member.search = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Member', method: 'search' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Member.togglePresence = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Member', method: 'togglePresence' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Member.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Member', method: 'render' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Member.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Member', method: 'clear' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Member.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Member', method: 'visible' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Section === undefined) {
    Ajax.App.Meeting.Session.Section = {};
}
Ajax.App.Meeting.Session.Section.pools = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Section', method: 'pools' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Section.savings = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Section', method: 'savings' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Section.credits = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Section', method: 'credits' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Section.cash = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Section', method: 'cash' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Section.charges = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Section', method: 'charges' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Section.reports = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Section', method: 'reports' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Section.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Section', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Section.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Section', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Section.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Section', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Session === undefined) {
    Ajax.App.Meeting.Session.Session = {};
}
Ajax.App.Meeting.Session.Session.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Session', method: 'home' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Session.resync = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Session', method: 'resync' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Session.open = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Session', method: 'open' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Session.close = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Session', method: 'close' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Session.saveAgenda = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Session', method: 'saveAgenda' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Session.saveReport = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Session', method: 'saveReport' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Session.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Session', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Session.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Session', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Session.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Session', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Summary.Summary === undefined) {
    Ajax.App.Meeting.Summary.Summary = {};
}
Ajax.App.Meeting.Summary.Summary.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Summary', method: 'home' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Summary.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Summary', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Summary.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Summary', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Summary.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Summary', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Tontine.Options.Options === undefined) {
    Ajax.App.Tontine.Options.Options = {};
}
Ajax.App.Tontine.Options.Options.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Options', method: 'home' }, { parameters: arguments, bags: ["tenant","charge"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Options.editOptions = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Options', method: 'editOptions' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Options.saveOptions = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Options', method: 'saveOptions' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Options.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Options', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Options.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Options', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Options.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Options', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Admin.User.Host.HostPage === undefined) {
    Ajax.App.Admin.User.Host.HostPage = {};
}
Ajax.App.Admin.User.Host.HostPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.HostPage', method: 'page' }, { parameters: arguments, bags: ["user"] });
};
Ajax.App.Admin.User.Host.HostPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.HostPage', method: 'render' }, { parameters: arguments, bags: ["user"] });
};
Ajax.App.Admin.User.Host.HostPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.HostPage', method: 'clear' }, { parameters: arguments, bags: ["user"] });
};
Ajax.App.Admin.User.Host.HostPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Host.HostPage', method: 'visible' }, { parameters: arguments, bags: ["user"] });
};
if(Ajax.App.Meeting.Presence.Session === undefined) {
    Ajax.App.Meeting.Presence.Session = {};
}
Ajax.App.Meeting.Presence.Session.togglePresence = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Session', method: 'togglePresence' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Session.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Session', method: 'render' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Session.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Session', method: 'clear' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Session.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Session', method: 'visible' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Session.Session === undefined) {
    Ajax.App.Planning.Session.Session = {};
}
Ajax.App.Planning.Session.Session.round = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'round' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'add' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'create' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.addList = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'addList' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.years = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'years' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.createList = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'createList' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'edit' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'update' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.editVenue = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'editVenue' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.saveVenue = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'saveVenue' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'delete' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'render' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'clear' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Session.Session.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.Session', method: 'visible' }, { parameters: arguments, bags: ["tenant","planning"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Remitment === undefined) {
    Ajax.App.Report.Session.Remitment = {};
}
Ajax.App.Report.Session.Remitment.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Remitment', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Remitment.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Remitment', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Remitment.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Remitment', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Tontine.Options.Category === undefined) {
    Ajax.App.Tontine.Options.Category = {};
}
Ajax.App.Tontine.Options.Category.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Category', method: 'add' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Category.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Category', method: 'create' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Category.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Category', method: 'edit' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Category.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Category', method: 'update' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Category.toggle = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Category', method: 'toggle' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Category.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Category', method: 'delete' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Category.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Category', method: 'render' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Category.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Category', method: 'clear' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Tontine.Options.Category.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.Category', method: 'visible' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Tontine.Options.FundPage === undefined) {
    Ajax.App.Tontine.Options.FundPage = {};
}
Ajax.App.Tontine.Options.FundPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.FundPage', method: 'page' }, { parameters: arguments, bags: ["tontine"] });
};
Ajax.App.Tontine.Options.FundPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.FundPage', method: 'render' }, { parameters: arguments, bags: ["tontine"] });
};
Ajax.App.Tontine.Options.FundPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.FundPage', method: 'clear' }, { parameters: arguments, bags: ["tontine"] });
};
Ajax.App.Tontine.Options.FundPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.FundPage', method: 'visible' }, { parameters: arguments, bags: ["tontine"] });
};
if(Ajax.App.Meeting.Presence.Presence === undefined) {
    Ajax.App.Meeting.Presence.Presence = {};
}
Ajax.App.Meeting.Presence.Presence.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Presence', method: 'home' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Presence.exchange = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Presence', method: 'exchange' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Presence.selectSession = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Presence', method: 'selectSession' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Presence.selectMember = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Presence', method: 'selectMember' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Presence.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Presence', method: 'render' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Presence.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Presence', method: 'clear' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Presence.Presence.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.Presence', method: 'visible' }, { parameters: arguments, bags: ["tenant","presence"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Bill.Total === undefined) {
    Ajax.App.Report.Session.Bill.Total = {};
}
Ajax.App.Report.Session.Bill.Total.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Bill.Total', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Bill.Total.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Bill.Total', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Bill.Total.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Bill.Total', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Tontine.Member.MemberPage === undefined) {
    Ajax.App.Tontine.Member.MemberPage = {};
}
Ajax.App.Tontine.Member.MemberPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.MemberPage', method: 'page' }, { parameters: arguments, bags: ["member"] });
};
Ajax.App.Tontine.Member.MemberPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.MemberPage', method: 'render' }, { parameters: arguments, bags: ["member"] });
};
Ajax.App.Tontine.Member.MemberPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.MemberPage', method: 'clear' }, { parameters: arguments, bags: ["member"] });
};
Ajax.App.Tontine.Member.MemberPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Member.MemberPage', method: 'visible' }, { parameters: arguments, bags: ["member"] });
};
if(Ajax.App.Admin.User.Guest.GuestPage === undefined) {
    Ajax.App.Admin.User.Guest.GuestPage = {};
}
Ajax.App.Admin.User.Guest.GuestPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.GuestPage', method: 'page' }, { parameters: arguments, bags: ["user"] });
};
Ajax.App.Admin.User.Guest.GuestPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.GuestPage', method: 'render' }, { parameters: arguments, bags: ["user"] });
};
Ajax.App.Admin.User.Guest.GuestPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.GuestPage', method: 'clear' }, { parameters: arguments, bags: ["user"] });
};
Ajax.App.Admin.User.Guest.GuestPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.GuestPage', method: 'visible' }, { parameters: arguments, bags: ["user"] });
};
if(Ajax.App.Planning.Pool.Session.Pool === undefined) {
    Ajax.App.Planning.Pool.Session.Pool = {};
}
Ajax.App.Planning.Pool.Session.Pool.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.Pool', method: 'render' }, { parameters: arguments, bags: ["tenant","pool","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.Pool.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.Pool', method: 'clear' }, { parameters: arguments, bags: ["tenant","pool","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.Pool.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.Pool', method: 'visible' }, { parameters: arguments, bags: ["tenant","pool","pool.session"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Session.RoundPage === undefined) {
    Ajax.App.Planning.Session.RoundPage = {};
}
Ajax.App.Planning.Session.RoundPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.RoundPage', method: 'page' }, { parameters: arguments, bags: ["planning"] });
};
Ajax.App.Planning.Session.RoundPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.RoundPage', method: 'render' }, { parameters: arguments, bags: ["planning"] });
};
Ajax.App.Planning.Session.RoundPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.RoundPage', method: 'clear' }, { parameters: arguments, bags: ["planning"] });
};
Ajax.App.Planning.Session.RoundPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.RoundPage', method: 'visible' }, { parameters: arguments, bags: ["planning"] });
};
if(Ajax.App.Planning.Subscription.Pool === undefined) {
    Ajax.App.Planning.Subscription.Pool = {};
}
Ajax.App.Planning.Subscription.Pool.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Pool', method: 'render' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Pool.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Pool', method: 'clear' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Pool.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Pool', method: 'visible' }, { parameters: arguments, bags: ["tenant","pool"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Action.Menu === undefined) {
    Ajax.App.Report.Session.Action.Menu = {};
}
Ajax.App.Report.Session.Action.Menu.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Action.Menu', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Action.Menu.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Action.Menu', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Action.Menu.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Action.Menu', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Saving.Fund === undefined) {
    Ajax.App.Report.Session.Saving.Fund = {};
}
Ajax.App.Report.Session.Saving.Fund.fund = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Saving.Fund', method: 'fund' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Saving.Fund.amount = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Saving.Fund', method: 'amount' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Saving.Fund.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Saving.Fund', method: 'render' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Saving.Fund.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Saving.Fund', method: 'clear' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Saving.Fund.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Saving.Fund', method: 'visible' }, { parameters: arguments, bags: ["tenant","report"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Tontine.Options.ChargePage === undefined) {
    Ajax.App.Tontine.Options.ChargePage = {};
}
Ajax.App.Tontine.Options.ChargePage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.ChargePage', method: 'page' }, { parameters: arguments, bags: ["charge"] });
};
Ajax.App.Tontine.Options.ChargePage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.ChargePage', method: 'render' }, { parameters: arguments, bags: ["charge"] });
};
Ajax.App.Tontine.Options.ChargePage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.ChargePage', method: 'clear' }, { parameters: arguments, bags: ["charge"] });
};
Ajax.App.Tontine.Options.ChargePage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.ChargePage', method: 'visible' }, { parameters: arguments, bags: ["charge"] });
};
if(Ajax.App.Meeting.Payment.PaymentPage === undefined) {
    Ajax.App.Meeting.Payment.PaymentPage = {};
}
Ajax.App.Meeting.Payment.PaymentPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.PaymentPage', method: 'page' }, { parameters: arguments, bags: ["payment"] });
};
Ajax.App.Meeting.Payment.PaymentPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.PaymentPage', method: 'render' }, { parameters: arguments, bags: ["payment"] });
};
Ajax.App.Meeting.Payment.PaymentPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.PaymentPage', method: 'clear' }, { parameters: arguments, bags: ["payment"] });
};
Ajax.App.Meeting.Payment.PaymentPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Payment.PaymentPage', method: 'visible' }, { parameters: arguments, bags: ["payment"] });
};
if(Ajax.App.Meeting.Presence.MemberPage === undefined) {
    Ajax.App.Meeting.Presence.MemberPage = {};
}
Ajax.App.Meeting.Presence.MemberPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.MemberPage', method: 'page' }, { parameters: arguments, bags: ["presence"] });
};
Ajax.App.Meeting.Presence.MemberPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.MemberPage', method: 'render' }, { parameters: arguments, bags: ["presence"] });
};
Ajax.App.Meeting.Presence.MemberPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.MemberPage', method: 'clear' }, { parameters: arguments, bags: ["presence"] });
};
Ajax.App.Meeting.Presence.MemberPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.MemberPage', method: 'visible' }, { parameters: arguments, bags: ["presence"] });
};
if(Ajax.App.Meeting.Session.Credit.Loan === undefined) {
    Ajax.App.Meeting.Session.Credit.Loan = {};
}
Ajax.App.Meeting.Session.Credit.Loan.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Loan', method: 'add' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Loan.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Loan', method: 'create' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Loan.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Loan', method: 'edit' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Loan.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Loan', method: 'update' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Loan.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Loan', method: 'delete' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Loan.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Loan', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Loan.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Loan', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Loan.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Loan', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.SessionPage === undefined) {
    Ajax.App.Meeting.Session.SessionPage = {};
}
Ajax.App.Meeting.Session.SessionPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.SessionPage', method: 'page' }, { parameters: arguments, bags: ["session"] });
};
Ajax.App.Meeting.Session.SessionPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.SessionPage', method: 'render' }, { parameters: arguments, bags: ["session"] });
};
Ajax.App.Meeting.Session.SessionPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.SessionPage', method: 'clear' }, { parameters: arguments, bags: ["session"] });
};
Ajax.App.Meeting.Session.SessionPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.SessionPage', method: 'visible' }, { parameters: arguments, bags: ["session"] });
};
if(Ajax.App.Report.Session.Bill.Session === undefined) {
    Ajax.App.Report.Session.Bill.Session = {};
}
Ajax.App.Report.Session.Bill.Session.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Bill.Session', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Bill.Session.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Bill.Session', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Bill.Session.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Bill.Session', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Disbursement === undefined) {
    Ajax.App.Report.Session.Disbursement = {};
}
Ajax.App.Report.Session.Disbursement.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Disbursement', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Disbursement.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Disbursement', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Disbursement.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Disbursement', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Presence.SessionPage === undefined) {
    Ajax.App.Meeting.Presence.SessionPage = {};
}
Ajax.App.Meeting.Presence.SessionPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.SessionPage', method: 'page' }, { parameters: arguments, bags: ["presence"] });
};
Ajax.App.Meeting.Presence.SessionPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.SessionPage', method: 'render' }, { parameters: arguments, bags: ["presence"] });
};
Ajax.App.Meeting.Presence.SessionPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.SessionPage', method: 'clear' }, { parameters: arguments, bags: ["presence"] });
};
Ajax.App.Meeting.Presence.SessionPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Presence.SessionPage', method: 'visible' }, { parameters: arguments, bags: ["presence"] });
};
if(Ajax.App.Meeting.Session.Cash.Balance === undefined) {
    Ajax.App.Meeting.Session.Cash.Balance = {};
}
Ajax.App.Meeting.Session.Cash.Balance.details = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Balance', method: 'details' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Balance.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Balance', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Balance.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Balance', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Balance.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Balance', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Session.SessionPage === undefined) {
    Ajax.App.Planning.Session.SessionPage = {};
}
Ajax.App.Planning.Session.SessionPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.SessionPage', method: 'page' }, { parameters: arguments, bags: ["planning"] });
};
Ajax.App.Planning.Session.SessionPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.SessionPage', method: 'render' }, { parameters: arguments, bags: ["planning"] });
};
Ajax.App.Planning.Session.SessionPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.SessionPage', method: 'clear' }, { parameters: arguments, bags: ["planning"] });
};
Ajax.App.Planning.Session.SessionPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Session.SessionPage', method: 'visible' }, { parameters: arguments, bags: ["planning"] });
};
if(Ajax.App.Planning.Subscription.Member === undefined) {
    Ajax.App.Planning.Subscription.Member = {};
}
Ajax.App.Planning.Subscription.Member.pool = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Member', method: 'pool' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Member.filter = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Member', method: 'filter' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Member.search = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Member', method: 'search' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Member.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Member', method: 'create' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Member.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Member', method: 'delete' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Member.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Member', method: 'render' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Member.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Member', method: 'clear' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Member.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Member', method: 'visible' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Report.Session.Action.Export === undefined) {
    Ajax.App.Report.Session.Action.Export = {};
}
Ajax.App.Report.Session.Action.Export.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Action.Export', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Action.Export.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Action.Export', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Report.Session.Action.Export.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Report.Session.Action.Export', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Tontine.Options.CategoryPage === undefined) {
    Ajax.App.Tontine.Options.CategoryPage = {};
}
Ajax.App.Tontine.Options.CategoryPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.CategoryPage', method: 'page' }, { parameters: arguments, bags: ["tontine"] });
};
Ajax.App.Tontine.Options.CategoryPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.CategoryPage', method: 'render' }, { parameters: arguments, bags: ["tontine"] });
};
Ajax.App.Tontine.Options.CategoryPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.CategoryPage', method: 'clear' }, { parameters: arguments, bags: ["tontine"] });
};
Ajax.App.Tontine.Options.CategoryPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Tontine.Options.CategoryPage', method: 'visible' }, { parameters: arguments, bags: ["tontine"] });
};
if(Ajax.App.Admin.User.Guest.Organisation === undefined) {
    Ajax.App.Admin.User.Guest.Organisation = {};
}
Ajax.App.Admin.User.Guest.Organisation.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.Organisation', method: 'render' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Guest.Organisation.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.Organisation', method: 'clear' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.User.Guest.Organisation.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.Organisation', method: 'visible' }, { parameters: arguments, bags: ["tenant","user"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Credit.Refund === undefined) {
    Ajax.App.Meeting.Session.Credit.Refund = {};
}
Ajax.App.Meeting.Session.Credit.Refund.fund = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Refund', method: 'fund' }, { parameters: arguments, bags: ["tenant","meeting","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Refund.toggleFilter = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Refund', method: 'toggleFilter' }, { parameters: arguments, bags: ["tenant","meeting","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Refund.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Refund', method: 'create' }, { parameters: arguments, bags: ["tenant","meeting","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Refund.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Refund', method: 'delete' }, { parameters: arguments, bags: ["tenant","meeting","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Refund.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Refund', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Refund.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Refund', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Refund.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Refund', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting","refund"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Saving.Amount === undefined) {
    Ajax.App.Meeting.Session.Saving.Amount = {};
}
Ajax.App.Meeting.Session.Saving.Amount.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Amount', method: 'edit' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Amount.save = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Amount', method: 'save' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Amount.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Amount', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Amount.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Amount', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Amount.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Amount', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Saving.Member === undefined) {
    Ajax.App.Meeting.Session.Saving.Member = {};
}
Ajax.App.Meeting.Session.Saving.Member.fund = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Member', method: 'fund' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Member.search = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Member', method: 'search' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Member.toggleFilter = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Member', method: 'toggleFilter' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Member.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Member', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Member.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Member', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Member.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Member', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Saving.Saving === undefined) {
    Ajax.App.Meeting.Session.Saving.Saving = {};
}
Ajax.App.Meeting.Session.Saving.Saving.fund = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Saving', method: 'fund' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Saving.editSaving = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Saving', method: 'editSaving' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Saving.updateSaving = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Saving', method: 'updateSaving' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Saving.deleteSaving = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Saving', method: 'deleteSaving' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Saving.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Saving', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Saving.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Saving', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Saving.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Saving', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Summary.Credit.Refund === undefined) {
    Ajax.App.Meeting.Summary.Credit.Refund = {};
}
Ajax.App.Meeting.Summary.Credit.Refund.fund = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.Refund', method: 'fund' }, { parameters: arguments, bags: ["tenant","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Credit.Refund.toggleFilter = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.Refund', method: 'toggleFilter' }, { parameters: arguments, bags: ["tenant","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Credit.Refund.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.Refund', method: 'render' }, { parameters: arguments, bags: ["tenant","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Credit.Refund.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.Refund', method: 'clear' }, { parameters: arguments, bags: ["tenant","refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Credit.Refund.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.Refund', method: 'visible' }, { parameters: arguments, bags: ["tenant","refund"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Summary.Saving.Saving === undefined) {
    Ajax.App.Meeting.Summary.Saving.Saving = {};
}
Ajax.App.Meeting.Summary.Saving.Saving.fund = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Saving.Saving', method: 'fund' }, { parameters: arguments, bags: ["tenant","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Saving.Saving.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Saving.Saving', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Saving.Saving.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Saving.Saving', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Saving.Saving.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Saving.Saving', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting.saving"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Pool.Session.Session === undefined) {
    Ajax.App.Planning.Pool.Session.Session = {};
}
Ajax.App.Planning.Pool.Session.Session.pool = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.Session', method: 'pool' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.Session.enableSession = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.Session', method: 'enableSession' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.Session.disableSession = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.Session', method: 'disableSession' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.Session.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.Session', method: 'render' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.Session.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.Session', method: 'clear' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.Session.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.Session', method: 'visible' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Credit.Balance === undefined) {
    Ajax.App.Meeting.Session.Credit.Balance = {};
}
Ajax.App.Meeting.Session.Credit.Balance.details = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Balance', method: 'details' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Balance.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Balance', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Balance.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Balance', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Balance.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Balance', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Saving.Closing === undefined) {
    Ajax.App.Meeting.Session.Saving.Closing = {};
}
Ajax.App.Meeting.Session.Saving.Closing.editRoundClosing = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Closing', method: 'editRoundClosing' }, { parameters: arguments, bags: ["tenant","meeting","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Closing.saveRoundClosing = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Closing', method: 'saveRoundClosing' }, { parameters: arguments, bags: ["tenant","meeting","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Closing.deleteRoundClosing = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Closing', method: 'deleteRoundClosing' }, { parameters: arguments, bags: ["tenant","meeting","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Closing.editInterestClosing = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Closing', method: 'editInterestClosing' }, { parameters: arguments, bags: ["tenant","meeting","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Closing.saveInterestClosing = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Closing', method: 'saveInterestClosing' }, { parameters: arguments, bags: ["tenant","meeting","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Closing.deleteInterestClosing = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Closing', method: 'deleteInterestClosing' }, { parameters: arguments, bags: ["tenant","meeting","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Closing.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Closing', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Closing.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Closing', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting","report"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Saving.Closing.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.Closing', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting","report"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Pool.Session.PoolPage === undefined) {
    Ajax.App.Planning.Pool.Session.PoolPage = {};
}
Ajax.App.Planning.Pool.Session.PoolPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.PoolPage', method: 'page' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Pool.Session.PoolPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.PoolPage', method: 'render' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Pool.Session.PoolPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.PoolPage', method: 'clear' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Pool.Session.PoolPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.PoolPage', method: 'visible' }, { parameters: arguments, bags: ["pool"] });
};
if(Ajax.App.Planning.Subscription.Planning === undefined) {
    Ajax.App.Planning.Subscription.Planning = {};
}
Ajax.App.Planning.Subscription.Planning.pool = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Planning', method: 'pool' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Planning.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Planning', method: 'render' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Planning.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Planning', method: 'clear' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Planning.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Planning', method: 'visible' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Subscription.PoolPage === undefined) {
    Ajax.App.Planning.Subscription.PoolPage = {};
}
Ajax.App.Planning.Subscription.PoolPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.PoolPage', method: 'page' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Subscription.PoolPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.PoolPage', method: 'render' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Subscription.PoolPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.PoolPage', method: 'clear' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Subscription.PoolPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.PoolPage', method: 'visible' }, { parameters: arguments, bags: ["pool"] });
};
if(Ajax.App.Admin.Organisation.Organisation === undefined) {
    Ajax.App.Admin.Organisation.Organisation = {};
}
Ajax.App.Admin.Organisation.Organisation.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.Organisation', method: 'home' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.Organisation.Organisation.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.Organisation', method: 'add' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.Organisation.Organisation.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.Organisation', method: 'create' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.Organisation.Organisation.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.Organisation', method: 'edit' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.Organisation.Organisation.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.Organisation', method: 'update' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.Organisation.Organisation.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.Organisation', method: 'delete' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.Organisation.Organisation.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.Organisation', method: 'render' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.Organisation.Organisation.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.Organisation', method: 'clear' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Admin.Organisation.Organisation.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.Organisation', method: 'visible' }, { parameters: arguments, bags: ["tenant","tontine"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Charge.Fixed.Fee === undefined) {
    Ajax.App.Meeting.Session.Charge.Fixed.Fee = {};
}
Ajax.App.Meeting.Session.Charge.Fixed.Fee.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Fee', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Fee.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Fee', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Fee.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Fee', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Charge.Libre.Fee === undefined) {
    Ajax.App.Meeting.Session.Charge.Libre.Fee = {};
}
Ajax.App.Meeting.Session.Charge.Libre.Fee.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Fee', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Fee.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Fee', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Fee.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Fee', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Pool.Session.EndSession === undefined) {
    Ajax.App.Planning.Pool.Session.EndSession = {};
}
Ajax.App.Planning.Pool.Session.EndSession.pool = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSession', method: 'pool' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.EndSession.save = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSession', method: 'save' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.EndSession.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSession', method: 'delete' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.EndSession.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSession', method: 'render' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.EndSession.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSession', method: 'clear' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.EndSession.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSession', method: 'visible' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Subscription.MemberPage === undefined) {
    Ajax.App.Planning.Subscription.MemberPage = {};
}
Ajax.App.Planning.Subscription.MemberPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.MemberPage', method: 'page' }, { parameters: arguments, bags: ["subscription"] });
};
Ajax.App.Planning.Subscription.MemberPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.MemberPage', method: 'render' }, { parameters: arguments, bags: ["subscription"] });
};
Ajax.App.Planning.Subscription.MemberPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.MemberPage', method: 'clear' }, { parameters: arguments, bags: ["subscription"] });
};
Ajax.App.Planning.Subscription.MemberPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.MemberPage', method: 'visible' }, { parameters: arguments, bags: ["subscription"] });
};
if(Ajax.App.Admin.User.Guest.OrganisationPage === undefined) {
    Ajax.App.Admin.User.Guest.OrganisationPage = {};
}
Ajax.App.Admin.User.Guest.OrganisationPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.OrganisationPage', method: 'page' }, { parameters: arguments, bags: ["user"] });
};
Ajax.App.Admin.User.Guest.OrganisationPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.OrganisationPage', method: 'render' }, { parameters: arguments, bags: ["user"] });
};
Ajax.App.Admin.User.Guest.OrganisationPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.OrganisationPage', method: 'clear' }, { parameters: arguments, bags: ["user"] });
};
Ajax.App.Admin.User.Guest.OrganisationPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.User.Guest.OrganisationPage', method: 'visible' }, { parameters: arguments, bags: ["user"] });
};
if(Ajax.App.Meeting.Session.Cash.Disbursement === undefined) {
    Ajax.App.Meeting.Session.Cash.Disbursement = {};
}
Ajax.App.Meeting.Session.Cash.Disbursement.addDisbursement = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Disbursement', method: 'addDisbursement' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Disbursement.createDisbursement = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Disbursement', method: 'createDisbursement' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Disbursement.editDisbursement = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Disbursement', method: 'editDisbursement' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Disbursement.updateDisbursement = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Disbursement', method: 'updateDisbursement' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Disbursement.deleteDisbursement = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Disbursement', method: 'deleteDisbursement' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Disbursement.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Disbursement', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Disbursement.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Disbursement', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Cash.Disbursement.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Cash.Disbursement', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Credit.RefundPage === undefined) {
    Ajax.App.Meeting.Session.Credit.RefundPage = {};
}
Ajax.App.Meeting.Session.Credit.RefundPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.RefundPage', method: 'page' }, { parameters: arguments, bags: ["meeting","refund"] });
};
Ajax.App.Meeting.Session.Credit.RefundPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.RefundPage', method: 'render' }, { parameters: arguments, bags: ["meeting","refund"] });
};
Ajax.App.Meeting.Session.Credit.RefundPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.RefundPage', method: 'clear' }, { parameters: arguments, bags: ["meeting","refund"] });
};
Ajax.App.Meeting.Session.Credit.RefundPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.RefundPage', method: 'visible' }, { parameters: arguments, bags: ["meeting","refund"] });
};
if(Ajax.App.Meeting.Session.Saving.MemberPage === undefined) {
    Ajax.App.Meeting.Session.Saving.MemberPage = {};
}
Ajax.App.Meeting.Session.Saving.MemberPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.MemberPage', method: 'page' }, { parameters: arguments, bags: ["meeting","meeting.saving"] });
};
Ajax.App.Meeting.Session.Saving.MemberPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.MemberPage', method: 'render' }, { parameters: arguments, bags: ["meeting","meeting.saving"] });
};
Ajax.App.Meeting.Session.Saving.MemberPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.MemberPage', method: 'clear' }, { parameters: arguments, bags: ["meeting","meeting.saving"] });
};
Ajax.App.Meeting.Session.Saving.MemberPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.MemberPage', method: 'visible' }, { parameters: arguments, bags: ["meeting","meeting.saving"] });
};
if(Ajax.App.Meeting.Session.Saving.SavingPage === undefined) {
    Ajax.App.Meeting.Session.Saving.SavingPage = {};
}
Ajax.App.Meeting.Session.Saving.SavingPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.SavingPage', method: 'page' }, { parameters: arguments, bags: ["meeting","meeting.saving"] });
};
Ajax.App.Meeting.Session.Saving.SavingPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.SavingPage', method: 'render' }, { parameters: arguments, bags: ["meeting","meeting.saving"] });
};
Ajax.App.Meeting.Session.Saving.SavingPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.SavingPage', method: 'clear' }, { parameters: arguments, bags: ["meeting","meeting.saving"] });
};
Ajax.App.Meeting.Session.Saving.SavingPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Saving.SavingPage', method: 'visible' }, { parameters: arguments, bags: ["meeting","meeting.saving"] });
};
if(Ajax.App.Meeting.Summary.Credit.RefundPage === undefined) {
    Ajax.App.Meeting.Summary.Credit.RefundPage = {};
}
Ajax.App.Meeting.Summary.Credit.RefundPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.RefundPage', method: 'page' }, { parameters: arguments, bags: ["refund"] });
};
Ajax.App.Meeting.Summary.Credit.RefundPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.RefundPage', method: 'render' }, { parameters: arguments, bags: ["refund"] });
};
Ajax.App.Meeting.Summary.Credit.RefundPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.RefundPage', method: 'clear' }, { parameters: arguments, bags: ["refund"] });
};
Ajax.App.Meeting.Summary.Credit.RefundPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.RefundPage', method: 'visible' }, { parameters: arguments, bags: ["refund"] });
};
if(Ajax.App.Meeting.Summary.Saving.SavingPage === undefined) {
    Ajax.App.Meeting.Summary.Saving.SavingPage = {};
}
Ajax.App.Meeting.Summary.Saving.SavingPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Saving.SavingPage', method: 'page' }, { parameters: arguments, bags: ["refund"] });
};
Ajax.App.Meeting.Summary.Saving.SavingPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Saving.SavingPage', method: 'render' }, { parameters: arguments, bags: ["refund"] });
};
Ajax.App.Meeting.Summary.Saving.SavingPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Saving.SavingPage', method: 'clear' }, { parameters: arguments, bags: ["refund"] });
};
Ajax.App.Meeting.Summary.Saving.SavingPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Saving.SavingPage', method: 'visible' }, { parameters: arguments, bags: ["refund"] });
};
if(Ajax.App.Planning.Pool.Session.SessionPage === undefined) {
    Ajax.App.Planning.Pool.Session.SessionPage = {};
}
Ajax.App.Planning.Pool.Session.SessionPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.SessionPage', method: 'page' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.SessionPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.SessionPage', method: 'render' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.SessionPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.SessionPage', method: 'clear' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.SessionPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.SessionPage', method: 'visible' }, { parameters: arguments, bags: ["pool.session"] });
};
if(Ajax.App.Planning.Subscription.Beneficiary === undefined) {
    Ajax.App.Planning.Subscription.Beneficiary = {};
}
Ajax.App.Planning.Subscription.Beneficiary.pool = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Beneficiary', method: 'pool' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Beneficiary.save = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Beneficiary', method: 'save' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Beneficiary.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Beneficiary', method: 'render' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Beneficiary.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Beneficiary', method: 'clear' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Beneficiary.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Beneficiary', method: 'visible' }, { parameters: arguments, bags: ["tenant","subscription"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Subscription.PoolSection === undefined) {
    Ajax.App.Planning.Subscription.PoolSection = {};
}
Ajax.App.Planning.Subscription.PoolSection.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.PoolSection', method: 'render' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Subscription.PoolSection.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.PoolSection', method: 'clear' }, { parameters: arguments, bags: ["pool"] });
};
Ajax.App.Planning.Subscription.PoolSection.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.PoolSection', method: 'visible' }, { parameters: arguments, bags: ["pool"] });
};
if(Ajax.App.Planning.Pool.Session.StartSession === undefined) {
    Ajax.App.Planning.Pool.Session.StartSession = {};
}
Ajax.App.Planning.Pool.Session.StartSession.pool = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSession', method: 'pool' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.StartSession.save = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSession', method: 'save' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.StartSession.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSession', method: 'delete' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.StartSession.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSession', method: 'render' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.StartSession.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSession', method: 'clear' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.StartSession.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSession', method: 'visible' }, { parameters: arguments, bags: ["tenant","pool.session"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Subscription.Subscription === undefined) {
    Ajax.App.Planning.Subscription.Subscription = {};
}
Ajax.App.Planning.Subscription.Subscription.home = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Subscription', method: 'home' }, { parameters: arguments, bags: ["tenant","planning","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Subscription.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Subscription', method: 'render' }, { parameters: arguments, bags: ["tenant","planning","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Subscription.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Subscription', method: 'clear' }, { parameters: arguments, bags: ["tenant","planning","subscription"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.Subscription.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.Subscription', method: 'visible' }, { parameters: arguments, bags: ["tenant","planning","subscription"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Admin.Organisation.OrganisationPage === undefined) {
    Ajax.App.Admin.Organisation.OrganisationPage = {};
}
Ajax.App.Admin.Organisation.OrganisationPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.OrganisationPage', method: 'page' }, { parameters: arguments, bags: ["tontine","pool"] });
};
Ajax.App.Admin.Organisation.OrganisationPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.OrganisationPage', method: 'render' }, { parameters: arguments, bags: ["tontine","pool"] });
};
Ajax.App.Admin.Organisation.OrganisationPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.OrganisationPage', method: 'clear' }, { parameters: arguments, bags: ["tontine","pool"] });
};
Ajax.App.Admin.Organisation.OrganisationPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Admin.Organisation.OrganisationPage', method: 'visible' }, { parameters: arguments, bags: ["tontine","pool"] });
};
if(Ajax.App.Meeting.Session.Charge.Libre.Amount === undefined) {
    Ajax.App.Meeting.Session.Charge.Libre.Amount = {};
}
Ajax.App.Meeting.Session.Charge.Libre.Amount.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Amount', method: 'edit' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Amount.save = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Amount', method: 'save' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Amount.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Amount', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Amount.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Amount', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Amount.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Amount', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Charge.Libre.Member === undefined) {
    Ajax.App.Meeting.Session.Charge.Libre.Member = {};
}
Ajax.App.Meeting.Session.Charge.Libre.Member.charge = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Member', method: 'charge' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Member.toggleFilter = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Member', method: 'toggleFilter' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Member.search = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Member', method: 'search' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Member.addBill = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Member', method: 'addBill' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Member.delBill = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Member', method: 'delBill' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Member.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Member', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Member.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Member', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Member.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Member', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Charge.Libre.Target === undefined) {
    Ajax.App.Meeting.Session.Charge.Libre.Target = {};
}
Ajax.App.Meeting.Session.Charge.Libre.Target.charge = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'charge' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Target.search = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'search' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Target.add = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'add' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Target.create = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'create' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Target.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'edit' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Target.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'update' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Target.remove = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'remove' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Target.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Target.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Target.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Target', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Pool.Deposit.Amount === undefined) {
    Ajax.App.Meeting.Session.Pool.Deposit.Amount = {};
}
Ajax.App.Meeting.Session.Pool.Deposit.Amount.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Amount', method: 'edit' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Amount.save = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Amount', method: 'save' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Amount.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Amount', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Amount.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Amount', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Amount.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Amount', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Subscription.MemberCounter === undefined) {
    Ajax.App.Planning.Subscription.MemberCounter = {};
}
Ajax.App.Planning.Subscription.MemberCounter.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.MemberCounter', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.MemberCounter.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.MemberCounter', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Subscription.MemberCounter.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Subscription.MemberCounter', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Charge.Fixed.FeePage === undefined) {
    Ajax.App.Meeting.Session.Charge.Fixed.FeePage = {};
}
Ajax.App.Meeting.Session.Charge.Fixed.FeePage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.FeePage', method: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Fixed.FeePage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.FeePage', method: 'render' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Fixed.FeePage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.FeePage', method: 'clear' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Fixed.FeePage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.FeePage', method: 'visible' }, { parameters: arguments, bags: ["meeting"] });
};
if(Ajax.App.Meeting.Session.Charge.Libre.FeePage === undefined) {
    Ajax.App.Meeting.Session.Charge.Libre.FeePage = {};
}
Ajax.App.Meeting.Session.Charge.Libre.FeePage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.FeePage', method: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.FeePage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.FeePage', method: 'render' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.FeePage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.FeePage', method: 'clear' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.FeePage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.FeePage', method: 'visible' }, { parameters: arguments, bags: ["meeting"] });
};
if(Ajax.App.Meeting.Session.Pool.Deposit.Deposit === undefined) {
    Ajax.App.Meeting.Session.Pool.Deposit.Deposit = {};
}
Ajax.App.Meeting.Session.Pool.Deposit.Deposit.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Deposit', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Deposit.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Deposit', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Deposit.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Deposit', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Summary.Credit.PartialRefund === undefined) {
    Ajax.App.Meeting.Summary.Credit.PartialRefund = {};
}
Ajax.App.Meeting.Summary.Credit.PartialRefund.fund = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.PartialRefund', method: 'fund' }, { parameters: arguments, bags: ["tenant","refund.partial"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Credit.PartialRefund.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.PartialRefund', method: 'render' }, { parameters: arguments, bags: ["tenant","refund.partial"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Credit.PartialRefund.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.PartialRefund', method: 'clear' }, { parameters: arguments, bags: ["tenant","refund.partial"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Summary.Credit.PartialRefund.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.PartialRefund', method: 'visible' }, { parameters: arguments, bags: ["tenant","refund.partial"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Pool.Session.EndSessionPage === undefined) {
    Ajax.App.Planning.Pool.Session.EndSessionPage = {};
}
Ajax.App.Planning.Pool.Session.EndSessionPage.current = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSessionPage', method: 'current' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.EndSessionPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSessionPage', method: 'page' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.EndSessionPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSessionPage', method: 'render' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.EndSessionPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSessionPage', method: 'clear' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.EndSessionPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.EndSessionPage', method: 'visible' }, { parameters: arguments, bags: ["pool.session"] });
};
if(Ajax.App.Planning.Pool.Session.SessionCounter === undefined) {
    Ajax.App.Planning.Pool.Session.SessionCounter = {};
}
Ajax.App.Planning.Pool.Session.SessionCounter.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.SessionCounter', method: 'render' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.SessionCounter.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.SessionCounter', method: 'clear' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Planning.Pool.Session.SessionCounter.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.SessionCounter', method: 'visible' }, { parameters: arguments, bags: ["tenant"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Credit.Partial.Amount === undefined) {
    Ajax.App.Meeting.Session.Credit.Partial.Amount = {};
}
Ajax.App.Meeting.Session.Credit.Partial.Amount.fund = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Amount', method: 'fund' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Amount.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Amount', method: 'edit' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Amount.save = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Amount', method: 'save' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Amount.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Amount', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Amount.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Amount', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Amount.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Amount', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Credit.Partial.Refund === undefined) {
    Ajax.App.Meeting.Session.Credit.Partial.Refund = {};
}
Ajax.App.Meeting.Session.Credit.Partial.Refund.fund = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Refund', method: 'fund' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Refund.edit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Refund', method: 'edit' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Refund.update = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Refund', method: 'update' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Refund.delete = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Refund', method: 'delete' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Refund.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Refund', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Refund.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Refund', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Credit.Partial.Refund.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.Refund', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting","partial.refund"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Pool.Remitment.Auction === undefined) {
    Ajax.App.Meeting.Session.Pool.Remitment.Auction = {};
}
Ajax.App.Meeting.Session.Pool.Remitment.Auction.toggleFilter = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Auction', method: 'toggleFilter' }, { parameters: arguments, bags: ["tenant","meeting","auction"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Auction.togglePayment = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Auction', method: 'togglePayment' }, { parameters: arguments, bags: ["tenant","meeting","auction"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Auction.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Auction', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting","auction"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Auction.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Auction', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting","auction"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Auction.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Auction', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting","auction"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Pool.Remitment.Payable === undefined) {
    Ajax.App.Meeting.Session.Pool.Remitment.Payable = {};
}
Ajax.App.Meeting.Session.Pool.Remitment.Payable.pool = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Payable', method: 'pool' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Payable.createRemitment = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Payable', method: 'createRemitment' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Payable.addRemitment = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Payable', method: 'addRemitment' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Payable.saveRemitment = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Payable', method: 'saveRemitment' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Payable.deleteRemitment = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Payable', method: 'deleteRemitment' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Payable.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Payable', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Payable.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Payable', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Payable.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Payable', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Planning.Pool.Session.StartSessionPage === undefined) {
    Ajax.App.Planning.Pool.Session.StartSessionPage = {};
}
Ajax.App.Planning.Pool.Session.StartSessionPage.current = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSessionPage', method: 'current' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.StartSessionPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSessionPage', method: 'page' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.StartSessionPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSessionPage', method: 'render' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.StartSessionPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSessionPage', method: 'clear' }, { parameters: arguments, bags: ["pool.session"] });
};
Ajax.App.Planning.Pool.Session.StartSessionPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Planning.Pool.Session.StartSessionPage', method: 'visible' }, { parameters: arguments, bags: ["pool.session"] });
};
if(Ajax.App.Meeting.Session.Charge.Fixed.Settlement === undefined) {
    Ajax.App.Meeting.Session.Charge.Fixed.Settlement = {};
}
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.charge = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'charge' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.toggleFilter = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'toggleFilter' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.search = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'search' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.addSettlement = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'addSettlement' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.delSettlement = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'delSettlement' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.addAllSettlements = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'addAllSettlements' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.delAllSettlements = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'delAllSettlements' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Fixed.Settlement.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.Settlement', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Charge.Libre.MemberPage === undefined) {
    Ajax.App.Meeting.Session.Charge.Libre.MemberPage = {};
}
Ajax.App.Meeting.Session.Charge.Libre.MemberPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.MemberPage', method: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.MemberPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.MemberPage', method: 'render' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.MemberPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.MemberPage', method: 'clear' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.MemberPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.MemberPage', method: 'visible' }, { parameters: arguments, bags: ["meeting"] });
};
if(Ajax.App.Meeting.Session.Charge.Libre.Settlement === undefined) {
    Ajax.App.Meeting.Session.Charge.Libre.Settlement = {};
}
Ajax.App.Meeting.Session.Charge.Libre.Settlement.charge = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Settlement', method: 'charge' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Settlement.toggleFilter = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Settlement', method: 'toggleFilter' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Settlement.addSettlement = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Settlement', method: 'addSettlement' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Settlement.delSettlement = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Settlement', method: 'delSettlement' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Settlement.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Settlement', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Settlement.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Settlement', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Charge.Libre.Settlement.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.Settlement', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Charge.Libre.TargetPage === undefined) {
    Ajax.App.Meeting.Session.Charge.Libre.TargetPage = {};
}
Ajax.App.Meeting.Session.Charge.Libre.TargetPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.TargetPage', method: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.TargetPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.TargetPage', method: 'render' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.TargetPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.TargetPage', method: 'clear' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.TargetPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.TargetPage', method: 'visible' }, { parameters: arguments, bags: ["meeting"] });
};
if(Ajax.App.Meeting.Session.Pool.Deposit.Receivable === undefined) {
    Ajax.App.Meeting.Session.Pool.Deposit.Receivable = {};
}
Ajax.App.Meeting.Session.Pool.Deposit.Receivable.pool = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Receivable', method: 'pool' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Receivable.addDeposit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Receivable', method: 'addDeposit' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Receivable.delDeposit = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Receivable', method: 'delDeposit' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Receivable.addAllDeposits = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Receivable', method: 'addAllDeposits' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Receivable.delAllDeposits = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Receivable', method: 'delAllDeposits' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Receivable.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Receivable', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Receivable.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Receivable', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Deposit.Receivable.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.Receivable', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Session.Pool.Remitment.Remitment === undefined) {
    Ajax.App.Meeting.Session.Pool.Remitment.Remitment = {};
}
Ajax.App.Meeting.Session.Pool.Remitment.Remitment.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Remitment', method: 'render' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Remitment.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Remitment', method: 'clear' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
Ajax.App.Meeting.Session.Pool.Remitment.Remitment.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.Remitment', method: 'visible' }, { parameters: arguments, bags: ["tenant","meeting"], callback: jaxon.ajax.callback.tontine });
};
if(Ajax.App.Meeting.Summary.Credit.PartialRefundPage === undefined) {
    Ajax.App.Meeting.Summary.Credit.PartialRefundPage = {};
}
Ajax.App.Meeting.Summary.Credit.PartialRefundPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.PartialRefundPage', method: 'page' }, { parameters: arguments, bags: ["refund.partial"] });
};
Ajax.App.Meeting.Summary.Credit.PartialRefundPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.PartialRefundPage', method: 'render' }, { parameters: arguments, bags: ["refund.partial"] });
};
Ajax.App.Meeting.Summary.Credit.PartialRefundPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.PartialRefundPage', method: 'clear' }, { parameters: arguments, bags: ["refund.partial"] });
};
Ajax.App.Meeting.Summary.Credit.PartialRefundPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Summary.Credit.PartialRefundPage', method: 'visible' }, { parameters: arguments, bags: ["refund.partial"] });
};
if(Ajax.App.Meeting.Session.Credit.Partial.RefundPage === undefined) {
    Ajax.App.Meeting.Session.Credit.Partial.RefundPage = {};
}
Ajax.App.Meeting.Session.Credit.Partial.RefundPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.RefundPage', method: 'page' }, { parameters: arguments, bags: ["meeting","refund.partial"] });
};
Ajax.App.Meeting.Session.Credit.Partial.RefundPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.RefundPage', method: 'render' }, { parameters: arguments, bags: ["meeting","refund.partial"] });
};
Ajax.App.Meeting.Session.Credit.Partial.RefundPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.RefundPage', method: 'clear' }, { parameters: arguments, bags: ["meeting","refund.partial"] });
};
Ajax.App.Meeting.Session.Credit.Partial.RefundPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Credit.Partial.RefundPage', method: 'visible' }, { parameters: arguments, bags: ["meeting","refund.partial"] });
};
if(Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage === undefined) {
    Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage = {};
}
Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage', method: 'page' }, { parameters: arguments, bags: ["meeting","auction"] });
};
Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage', method: 'render' }, { parameters: arguments, bags: ["meeting","auction"] });
};
Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage', method: 'clear' }, { parameters: arguments, bags: ["meeting","auction"] });
};
Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Remitment.AuctionPage', method: 'visible' }, { parameters: arguments, bags: ["meeting","auction"] });
};
if(Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage === undefined) {
    Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage = {};
}
Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage', method: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage', method: 'render' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage', method: 'clear' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Fixed.SettlementPage', method: 'visible' }, { parameters: arguments, bags: ["meeting"] });
};
if(Ajax.App.Meeting.Session.Charge.Libre.SettlementPage === undefined) {
    Ajax.App.Meeting.Session.Charge.Libre.SettlementPage = {};
}
Ajax.App.Meeting.Session.Charge.Libre.SettlementPage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.SettlementPage', method: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.SettlementPage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.SettlementPage', method: 'render' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.SettlementPage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.SettlementPage', method: 'clear' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Charge.Libre.SettlementPage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Charge.Libre.SettlementPage', method: 'visible' }, { parameters: arguments, bags: ["meeting"] });
};
if(Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage === undefined) {
    Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage = {};
}
Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage.page = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage', method: 'page' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage.render = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage', method: 'render' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage.clear = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage', method: 'clear' }, { parameters: arguments, bags: ["meeting"] });
};
Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage.visible = function() {
    return jaxon.request({ type: 'class', name: 'Ajax.App.Meeting.Session.Pool.Deposit.ReceivablePage', method: 'visible' }, { parameters: arguments, bags: ["meeting"] });
};

jaxon.dialog.config({"labels":{"confirm":{"yes":"Oui","no":"Non"}},"defaults":{"modal":"bootbox","alert":"toastr","confirm":"noty"},"options":{"toastr":{"alert":{"closeButton":true,"closeDuration":0,"positionClass":"toast-top-center"}}}});

jaxon.dom.ready(function() {

    jaxon.processCustomAttrs();

});