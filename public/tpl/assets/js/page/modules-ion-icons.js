"use strict";

jq("#icons li").each(function() {
  jq(this).append('<div class="icon-name">'+ jq(this).attr('class') +'</div>');
});
jq("#icons li").click(function() {
  jq(".icon-name").fadeOut();
  jq(this).find('.icon-name').fadeIn();
});
