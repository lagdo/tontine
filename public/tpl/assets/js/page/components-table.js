"use strict";

jq("[data-checkboxes]").each(function() {
  var me = jq(this),
    group = me.data('checkboxes'),
    role = me.data('checkbox-role');

  me.change(function() {
    var all = jq('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"])'),
      checked = jq('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"]):checked'),
      dad = jq('[data-checkboxes="' + group + '"][data-checkbox-role="dad"]'),
      total = all.length,
      checked_length = checked.length;

    if(role == 'dad') {
      if(me.is(':checked')) {
        all.prop('checked', true);
      }else{
        all.prop('checked', false);
      }
    }else{
      if(checked_length >= total) {
        dad.prop('checked', true);
      }else{
        dad.prop('checked', false);
      }
    }
  });
});

jq("#sortable-table tbody").sortable({
  handle: '.sort-handler'
});
