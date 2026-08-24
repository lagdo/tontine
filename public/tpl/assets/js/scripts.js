"use strict";

// ChartJS
if(window.Chart) {
  Chart.defaults.global.defaultFontFamily = "'Nunito', 'Segoe UI', 'Arial'";
  Chart.defaults.global.defaultFontSize = 12;
  Chart.defaults.global.defaultFontStyle = 500;
  Chart.defaults.global.defaultFontColor = "#999";
  Chart.defaults.global.tooltips.backgroundColor = "#000";
  Chart.defaults.global.tooltips.bodyFontColor = "rgba(255,255,255,.7)";
  Chart.defaults.global.tooltips.titleMarginBottom = 10;
  Chart.defaults.global.tooltips.titleFontSize = 14;
  Chart.defaults.global.tooltips.titleFontFamily = "'Nunito', 'Segoe UI', 'Arial'";
  Chart.defaults.global.tooltips.titleFontColor = '#fff';
  Chart.defaults.global.tooltips.xPadding = 15;
  Chart.defaults.global.tooltips.yPadding = 15;
  Chart.defaults.global.tooltips.displayColors = false;
  Chart.defaults.global.tooltips.intersect = false;
  Chart.defaults.global.tooltips.mode = 'nearest';
}

// DropzoneJS
if(window.Dropzone) {
  Dropzone.autoDiscover = false;
}

// Basic confirm box
jq('[data-confirm]').each(function() {
  var me = jq(this),
      me_data = me.data('confirm');

  me_data = me_data.split("|");
  me.fireModal({
    title: me_data[0],
    body: me_data[1],
    buttons: [
      {
        text: me.data('confirm-text-yes') || 'Yes',
        class: 'btn btn-danger btn-shadow',
        handler: function() {
          eval(me.data('confirm-yes'));
        }
      },
      {
        text: me.data('confirm-text-cancel') || 'Cancel',
        class: 'btn btn-secondary',
        handler: function(modal) {
          $.destroyModal(modal);
          eval(me.data('confirm-no'));
        }
      }
    ]
  })
});

// Global
jq(function() {
  let sidebar_nicescroll_opts = {
    cursoropacitymin: 0,
    cursoropacitymax: .8,
    zindex: 892
  }, now_layout_class = null;

  var sidebar_sticky = function() {
    if(jq("body").hasClass('layout-2')) {
      jq("body.layout-2 #sidebar-wrapper").stick_in_parent({
        parent: jq('body')
      });
      jq("body.layout-2 #sidebar-wrapper").stick_in_parent({recalc_every: 1});
    }
  }
  sidebar_sticky();

  var sidebar_nicescroll;
  var update_sidebar_nicescroll = function() {
    let a = setInterval(function() {
      if(sidebar_nicescroll != null)
        sidebar_nicescroll.resize();
    }, 10);

    setTimeout(function() {
      clearInterval(a);
    }, 600);
  }

  var sidebar_dropdown = function() {
    if(jq(".main-sidebar").length) {
      jq(".main-sidebar").niceScroll(sidebar_nicescroll_opts);
      sidebar_nicescroll = jq(".main-sidebar").getNiceScroll();

      jq(".main-sidebar .sidebar-menu li a.has-dropdown").off('click').on('click', function() {
        var me     = jq(this);
        var active = false;
        if(me.parent().hasClass("active")){
          active = true;
        }

        jq('.main-sidebar .sidebar-menu li.active > .dropdown-menu').slideUp(500, function() {
          update_sidebar_nicescroll();
          return false;
        });

        jq('.main-sidebar .sidebar-menu li.active').removeClass('active');

        if(active==true) {
          me.parent().removeClass('active');
          me.parent().find('> .dropdown-menu').slideUp(500, function() {
            update_sidebar_nicescroll();
            return false;
          });
        }else{
          me.parent().addClass('active');
          me.parent().find('> .dropdown-menu').slideDown(500, function() {
            update_sidebar_nicescroll();
            return false;
          });
        }

        return false;
      });

      jq('.main-sidebar .sidebar-menu li.active > .dropdown-menu').slideDown(500, function() {
        update_sidebar_nicescroll();
        return false;
      });
    }
  }
  sidebar_dropdown();

  if(jq("#top-5-scroll").length) {
    jq("#top-5-scroll").css({
      height: 315
    }).niceScroll();
  }

  jq(".main-content").css({
    minHeight: jq(window).outerHeight() - 108
  })

  jq(".nav-collapse-toggle").click(function() {
    jq(this).parent().find('.navbar-nav').toggleClass('show');
    return false;
  });

  jq(document).on('click', function(e) {
    jq(".nav-collapse .navbar-nav").removeClass('show');
  });

  var toggle_sidebar_mini = function(mini) {
    let body = jq('body');

    if(!mini) {
      body.removeClass('sidebar-mini');
      jq(".main-sidebar").css({
        overflow: 'hidden'
      });
      setTimeout(function() {
        jq(".main-sidebar").niceScroll(sidebar_nicescroll_opts);
        sidebar_nicescroll = jq(".main-sidebar").getNiceScroll();
      }, 500);
      jq(".main-sidebar .sidebar-menu > li > ul .dropdown-title").remove();
      jq(".main-sidebar .sidebar-menu > li > a").removeAttr('data-toggle');
      jq(".main-sidebar .sidebar-menu > li > a").removeAttr('data-original-title');
      jq(".main-sidebar .sidebar-menu > li > a").removeAttr('title');
    }else{
      body.addClass('sidebar-mini');
      body.removeClass('sidebar-show');
      sidebar_nicescroll.remove();
      sidebar_nicescroll = null;
      jq(".main-sidebar .sidebar-menu > li").each(function() {
        let me = jq(this);

        if(me.find('> .dropdown-menu').length) {
          me.find('> .dropdown-menu').hide();
          me.find('> .dropdown-menu').prepend('<li class="dropdown-title pt-3">'+ me.find('> a').text() +'</li>');
        }else{
          me.find('> a').attr('data-toggle', 'tooltip');
          me.find('> a').attr('data-original-title', me.find('> a').text());
          jq("[data-toggle='tooltip']").tooltip({
            placement: 'right'
          });
        }
      });
    }
  }

  jq("[data-toggle='sidebar']").click(function() {
    var body = jq("body"),
      w = jq(window);

    if(w.outerWidth() <= 1024) {
      body.removeClass('search-show search-gone');
      if(body.hasClass('sidebar-gone')) {
        body.removeClass('sidebar-gone');
        body.addClass('sidebar-show');
      }else{
        body.addClass('sidebar-gone');
        body.removeClass('sidebar-show');
      }

      update_sidebar_nicescroll();
    }else{
      body.removeClass('search-show search-gone');
      if(body.hasClass('sidebar-mini')) {
        toggle_sidebar_mini(false);
      }else{
        toggle_sidebar_mini(true);
      }
    }

    return false;
  });

  var toggleLayout = function() {
    var w = jq(window),
      layout_class = jq('body').attr('class') || '',
      layout_classes = (layout_class.trim().length > 0 ? layout_class.split(' ') : '');

    if(layout_classes.length > 0) {
      layout_classes.forEach(function(item) {
        if(item.indexOf('layout-') != -1) {
          now_layout_class = item;
        }
      });
    }

    if(w.outerWidth() <= 1024) {
      if(jq('body').hasClass('sidebar-mini')) {
        toggle_sidebar_mini(false);
        jq('.main-sidebar').niceScroll(sidebar_nicescroll_opts);
        sidebar_nicescroll = jq(".main-sidebar").getNiceScroll();
      }

      jq("body").addClass("sidebar-gone");
      jq("body").removeClass("layout-2 layout-3 sidebar-mini sidebar-show");
      jq("body").off('click touchend').on('click touchend', function(e) {
        if(jq(e.target).hasClass('sidebar-show') || jq(e.target).hasClass('search-show')) {
          jq("body").removeClass("sidebar-show");
          jq("body").addClass("sidebar-gone");
          jq("body").removeClass("search-show");

          update_sidebar_nicescroll();
        }
      });

      update_sidebar_nicescroll();

      if(now_layout_class == 'layout-3') {
        let nav_second_classes = jq(".navbar-secondary").attr('class'),
          nav_second = jq(".navbar-secondary");

        nav_second.attr('data-nav-classes', nav_second_classes);
        nav_second.removeAttr('class');
        nav_second.addClass('main-sidebar');

        let main_sidebar = jq(".main-sidebar");
        main_sidebar.find('.container').addClass('sidebar-wrapper').removeClass('container');
        main_sidebar.find('.navbar-nav').addClass('sidebar-menu').removeClass('navbar-nav');
        main_sidebar.find('.sidebar-menu .nav-item.dropdown.show a').click();
        main_sidebar.find('.sidebar-brand').remove();
        main_sidebar.find('.sidebar-menu').before(jq('<div>', {
          class: 'sidebar-brand'
        }).append(
          jq('<a>', {
            href: jq('.navbar-brand').attr('href'),
          }).html(jq('.navbar-brand').html())
        ));
        setTimeout(function() {
          sidebar_nicescroll = main_sidebar.niceScroll(sidebar_nicescroll_opts);
          sidebar_nicescroll = main_sidebar.getNiceScroll();
        }, 700);

        sidebar_dropdown();
        jq(".main-wrapper").removeClass("container");
      }
    }else{
      jq("body").removeClass("sidebar-gone sidebar-show");
      if(now_layout_class)
        jq("body").addClass(now_layout_class);

      let nav_second_classes = jq(".main-sidebar").attr('data-nav-classes'),
        nav_second = jq(".main-sidebar");

      if(now_layout_class == 'layout-3' && nav_second.hasClass('main-sidebar')) {
        nav_second.find(".sidebar-menu li a.has-dropdown").off('click');
        nav_second.find('.sidebar-brand').remove();
        nav_second.removeAttr('class');
        nav_second.addClass(nav_second_classes);

        let main_sidebar = jq(".navbar-secondary");
        main_sidebar.find('.sidebar-wrapper').addClass('container').removeClass('sidebar-wrapper');
        main_sidebar.find('.sidebar-menu').addClass('navbar-nav').removeClass('sidebar-menu');
        main_sidebar.find('.dropdown-menu').hide();
        main_sidebar.removeAttr('style');
        main_sidebar.removeAttr('tabindex');
        main_sidebar.removeAttr('data-nav-classes');
        jq(".main-wrapper").addClass("container");
        // if(sidebar_nicescroll != null)
        //   sidebar_nicescroll.remove();
      }else if(now_layout_class == 'layout-2') {
        jq("body").addClass("layout-2");
      }else{
        update_sidebar_nicescroll();
      }
    }
  }
  toggleLayout();
  jq(window).resize(toggleLayout);

  jq("[data-toggle='search']").click(function() {
    var body = jq("body");

    if(body.hasClass('search-gone')) {
      body.addClass('search-gone');
      body.removeClass('search-show');
    }else{
      body.removeClass('search-gone');
      body.addClass('search-show');
    }
  });

  // tooltip
  jq("[data-toggle='tooltip']").tooltip();

  // popover
  jq('[data-toggle="popover"]').popover({
    container: 'body'
  });

  // Select2
  if(jQuery().select2) {
    jq(".select2").select2();
  }

  // Selectric
  if(jQuery().selectric) {
    jq(".selectric").selectric({
      disableOnMobile: false,
      nativeOnMobile: false
    });
  }

  jq(".notification-toggle").dropdown();
  jq(".notification-toggle").parent().on('shown.bs.dropdown', function() {
    jq(".dropdown-list-icons").niceScroll({
      cursoropacitymin: .3,
      cursoropacitymax: .8,
      cursorwidth: 7
    });
  });

  jq(".message-toggle").dropdown();
  jq(".message-toggle").parent().on('shown.bs.dropdown', function() {
    jq(".dropdown-list-message").niceScroll({
      cursoropacitymin: .3,
      cursoropacitymax: .8,
      cursorwidth: 7
    });
  });

  if(jq(".chat-content").length) {
    jq(".chat-content").niceScroll({
        cursoropacitymin: .3,
        cursoropacitymax: .8,
    });
    jq('.chat-content').getNiceScroll(0).doScrollTop(jq('.chat-content').height());
  }

  if(jQuery().summernote) {
    jq(".summernote").summernote({
       dialogsInBody: true,
      minHeight: 250,
    });
    jq(".summernote-simple").summernote({
       dialogsInBody: true,
      minHeight: 150,
      toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]
      ]
    });
  }

  if(window.CodeMirror) {
    jq(".codeeditor").each(function() {
      let editor = CodeMirror.fromTextArea(this, {
        lineNumbers: true,
        theme: "duotone-dark",
        mode: 'javascript',
        height: 200
      });
      editor.setSize("100%", 200);
    });
  }

  // Follow function
  jq('.follow-btn, .following-btn').each(function() {
    var me = jq(this),
        follow_text = 'Follow',
        unfollow_text = 'Following';

    me.click(function() {
      if(me.hasClass('following-btn')) {
        me.removeClass('btn-danger');
        me.removeClass('following-btn');
        me.addClass('btn-primary');
        me.html(follow_text);

        eval(me.data('unfollow-action'));
      }else{
        me.removeClass('btn-primary');
        me.addClass('btn-danger');
        me.addClass('following-btn');
        me.html(unfollow_text);

        eval(me.data('follow-action'));
      }
      return false;
    });
  });

  // Dismiss function
  jq("[data-dismiss]").each(function() {
    var me = jq(this),
        target = me.data('dismiss');

    me.click(function() {
      jq(target).fadeOut(function() {
        jq(target).remove();
      });
      return false;
    });
  });

  // Collapsable
  jq("[data-collapse]").each(function() {
    var me = jq(this),
        target = me.data('collapse');

    me.click(function() {
      jq(target).collapse('toggle');
      jq(target).on('shown.bs.collapse', function(e) {
        e.stopPropagation();
        me.html('<i class="fas fa-minus"></i>');
      });
      jq(target).on('hidden.bs.collapse', function(e) {
        e.stopPropagation();
        me.html('<i class="fas fa-plus"></i>');
      });
      return false;
    });
  });

  // Gallery
  jq(".gallery .gallery-item").each(function() {
    var me = jq(this);

    me.attr('href', me.data('image'));
    me.attr('title', me.data('title'));
    if(me.parent().hasClass('gallery-fw')) {
      me.css({
        height: me.parent().data('item-height'),
      });
      me.find('div').css({
        lineHeight: me.parent().data('item-height') + 'px'
      });
    }
    me.css({
      backgroundImage: 'url("'+ me.data('image') +'")'
    });
  });
  if(jQuery().Chocolat) {
    jq(".gallery").Chocolat({
      className: 'gallery',
      imageSelector: '.gallery-item',
    });
  }

  // Background
  jq("[data-background]").each(function() {
    var me = jq(this);
    me.css({
      backgroundImage: 'url(' + me.data('background') + ')'
    });
  });

  // Custom Tab
  jq("[data-tab]").each(function() {
    var me = jq(this);

    me.click(function() {
      if(!me.hasClass('active')) {
        var tab_group = jq('[data-tab-group="' + me.data('tab') + '"]'),
            tab_group_active = jq('[data-tab-group="' + me.data('tab') + '"].active'),
            target = jq(me.attr('href')),
            links = jq('[data-tab="'+me.data('tab') +'"]');

        links.removeClass('active');
        me.addClass('active');
        target.addClass('active');
        tab_group_active.removeClass('active');
      }
      return false;
    });
  });

  // Bootstrap 4 Validation
  jq(".needs-validation").submit(function() {
    var form = jq(this);
    if (form[0].checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.addClass('was-validated');
  });

  // alert dismissible
  jq(".alert-dismissible").each(function() {
    var me = jq(this);

    me.find('.close').click(function() {
      me.alert('close');
    });
  });

  if(jq('.main-navbar').length) {
  }

  // Image cropper
  jq('[data-crop-image]').each(function(e) {
    jq(this).css({
      overflow: 'hidden',
      position: 'relative',
      height: jq(this).data('crop-image')
    });
  });

  // Slide Toggle
  jq('[data-toggle-slide]').click(function() {
    let target = jq(this).data('toggle-slide');

    jq(target).slideToggle();
    return false;
  });

  // Dismiss modal
  jq("[data-dismiss=modal]").click(function() {
    jq(this).closest('.modal').modal('hide');

    return false;
  });

  // Width attribute
  jq('[data-width]').each(function() {
    jq(this).css({
      width: jq(this).data('width')
    });
  });

  // Height attribute
  jq('[data-height]').each(function() {
    jq(this).css({
      height: jq(this).data('height')
    });
  });

  // Chocolat
  if(jq('.chocolat-parent').length && jQuery().Chocolat) {
    jq('.chocolat-parent').Chocolat();
  }

  // Sortable card
  if(jq('.sortable-card').length && jQuery().sortable) {
    jq('.sortable-card').sortable({
      handle: '.card-header',
      opacity: .8,
      tolerance: 'pointer'
    });
  }

  // Daterangepicker
  if(jQuery().daterangepicker) {
    if(jq(".datepicker").length) {
      jq('.datepicker').daterangepicker({
        locale: {format: 'YYYY-MM-DD'},
        singleDatePicker: true,
      });
    }
    if(jq(".datetimepicker").length) {
      jq('.datetimepicker').daterangepicker({
        locale: {format: 'YYYY-MM-DD HH:mm'},
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
      });
    }
    if(jq(".daterange").length) {
      jq('.daterange').daterangepicker({
        locale: {format: 'YYYY-MM-DD'},
        drops: 'down',
        opens: 'right'
      });
    }
  }

  // Timepicker
  if(jQuery().timepicker && jq(".timepicker").length) {
    jq(".timepicker").timepicker({
      icons: {
        up: 'fas fa-chevron-up',
        down: 'fas fa-chevron-down'
      }
    });
  }
});
