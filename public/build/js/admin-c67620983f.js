/*! AdminLTE app.js
 * ================
 * Main JS application file for AdminLTE v2. This file
 * should be included in all pages. It controls some layout
 * options and implements exclusive AdminLTE plugins.
 *
 * @Author  Almsaeed Studio
 * @Support <http://www.almsaeedstudio.com>
 * @Email   <abdullah@almsaeedstudio.com>
 * @version 2.3.7
 * @license MIT <http://opensource.org/licenses/MIT>
 */

//Make sure jQuery has been loaded before app.js
if (typeof jQuery === "undefined") {
  throw new Error("AdminLTE requires jQuery");
}

/* AdminLTE
 *
 * @type Object
 * @description $.AdminLTE is the main object for the template's app.
 *              It's used for implementing functions and options related
 *              to the template. Keeping everything wrapped in an object
 *              prevents conflict with other plugins and is a better
 *              way to organize our code.
 */
$.AdminLTE = {};

/* --------------------
 * - AdminLTE Options -
 * --------------------
 * Modify these options to suit your implementation
 */
$.AdminLTE.options = {
  //Add slimscroll to navbar menus
  //This requires you to load the slimscroll plugin
  //in every page before app.js
  navbarMenuSlimscroll: true,
  navbarMenuSlimscrollWidth: "3px", //The width of the scroll bar
  navbarMenuHeight: "200px", //The height of the inner menu
  //General animation speed for JS animated elements such as box collapse/expand and
  //sidebar treeview slide up/down. This options accepts an integer as milliseconds,
  //'fast', 'normal', or 'slow'
  animationSpeed: 'fast',
  //Sidebar push menu toggle button selector
  sidebarToggleSelector: "[data-toggle='offcanvas']",
  //Activate sidebar push menu
  sidebarPushMenu: true,
  //Activate sidebar slimscroll if the fixed layout is set (requires SlimScroll Plugin)
  sidebarSlimScroll: true,
  //Enable sidebar expand on hover effect for sidebar mini
  //This option is forced to true if both the fixed layout and sidebar mini
  //are used together
  sidebarExpandOnHover: false,
  //BoxRefresh Plugin
  enableBoxRefresh: true,
  //Bootstrap.js tooltip
  enableBSToppltip: true,
  BSTooltipSelector: "[data-toggle='tooltip']",
  //Enable Fast Click. Fastclick.js creates a more
  //native touch experience with touch devices. If you
  //choose to enable the plugin, make sure you load the script
  //before AdminLTE's app.js
  enableFastclick: false,
  //Control Sidebar Options
  enableControlSidebar: true,
  controlSidebarOptions: {
    //Which button should trigger the open/close event
    toggleBtnSelector: "[data-toggle='control-sidebar']",
    //The sidebar selector
    selector: ".control-sidebar",
    //Enable slide over content
    slide: true
  },
  //Box Widget Plugin. Enable this plugin
  //to allow boxes to be collapsed and/or removed
  enableBoxWidget: true,
  //Box Widget plugin options
  boxWidgetOptions: {
    boxWidgetIcons: {
      //Collapse icon
      collapse: 'fa-minus',
      //Open icon
      open: 'fa-plus',
      //Remove icon
      remove: 'fa-times'
    },
    boxWidgetSelectors: {
      //Remove button selector
      remove: '[data-widget="remove"]',
      //Collapse button selector
      collapse: '[data-widget="collapse"]'
    }
  },
  //Direct Chat plugin options
  directChat: {
    //Enable direct chat by default
    enable: true,
    //The button to open and close the chat contacts pane
    contactToggleSelector: '[data-widget="chat-pane-toggle"]'
  },
  //Define the set of colors to use globally around the website
  colors: {
    lightBlue: "#3c8dbc",
    red: "#f56954",
    green: "#00a65a",
    aqua: "#00c0ef",
    yellow: "#f39c12",
    blue: "#0073b7",
    navy: "#001F3F",
    teal: "#39CCCC",
    olive: "#3D9970",
    lime: "#01FF70",
    orange: "#FF851B",
    fuchsia: "#F012BE",
    purple: "#8E24AA",
    maroon: "#D81B60",
    black: "#222222",
    gray: "#d2d6de"
  },
  //The standard screen sizes that bootstrap uses.
  //If you change these in the variables.less file, change
  //them here too.
  screenSizes: {
    xs: 480,
    sm: 768,
    md: 992,
    lg: 1200
  }
};

/* ------------------
 * - Implementation -
 * ------------------
 * The next block of code implements AdminLTE's
 * functions and plugins as specified by the
 * options above.
 */
$(function () {
  "use strict";

  //Fix for IE page transitions
  $("body").removeClass("hold-transition");

  //Extend options if external options exist
  if (typeof AdminLTEOptions !== "undefined") {
    $.extend(true,
      $.AdminLTE.options,
      AdminLTEOptions);
  }

  //Easy access to options
  var o = $.AdminLTE.options;

  //Set up the object
  _init();

  //Activate the layout maker
  $.AdminLTE.layout.activate();

  //Enable sidebar tree view controls
  $.AdminLTE.tree('.sidebar');

  //Enable control sidebar
  if (o.enableControlSidebar) {
    $.AdminLTE.controlSidebar.activate();
  }

  //Add slimscroll to navbar dropdown
  if (o.navbarMenuSlimscroll && typeof $.fn.slimscroll != 'undefined') {
    $(".navbar .menu").slimscroll({
      height: o.navbarMenuHeight,
      alwaysVisible: false,
      size: o.navbarMenuSlimscrollWidth
    }).css("width", "100%");
  }

  //Activate sidebar push menu
  if (o.sidebarPushMenu) {
    $.AdminLTE.pushMenu.activate(o.sidebarToggleSelector);
  }

  //Activate Bootstrap tooltip
  if (o.enableBSToppltip) {
    $('body').tooltip({
      selector: o.BSTooltipSelector
    });
  }

  //Activate box widget
  if (o.enableBoxWidget) {
    $.AdminLTE.boxWidget.activate();
  }

  //Activate fast click
  if (o.enableFastclick && typeof FastClick != 'undefined') {
    FastClick.attach(document.body);
  }

  //Activate direct chat widget
  if (o.directChat.enable) {
    $(document).on('click', o.directChat.contactToggleSelector, function () {
      var box = $(this).parents('.direct-chat').first();
      box.toggleClass('direct-chat-contacts-open');
    });
  }

  /*
   * INITIALIZE BUTTON TOGGLE
   * ------------------------
   */
  $('.btn-group[data-toggle="btn-toggle"]').each(function () {
    var group = $(this);
    $(this).find(".btn").on('click', function (e) {
      group.find(".btn.active").removeClass("active");
      $(this).addClass("active");
      e.preventDefault();
    });

  });
});

/* ----------------------------------
 * - Initialize the AdminLTE Object -
 * ----------------------------------
 * All AdminLTE functions are implemented below.
 */
function _init() {
  'use strict';
  /* Layout
   * ======
   * Fixes the layout height in case min-height fails.
   *
   * @type Object
   * @usage $.AdminLTE.layout.activate()
   *        $.AdminLTE.layout.fix()
   *        $.AdminLTE.layout.fixSidebar()
   */
  $.AdminLTE.layout = {
    activate: function () {
      var _this = this;
      _this.fix();
      _this.fixSidebar();
      $(window, ".wrapper").resize(function () {
        _this.fix();
        _this.fixSidebar();
      });
    },
    fix: function () {
      //Get window height and the wrapper height
      var neg = $('.main-header').outerHeight() + $('.main-footer').outerHeight();
      var window_height = $(window).height();
      var sidebar_height = $(".sidebar").height();
      //Set the min-height of the content and sidebar based on the
      //the height of the document.
      if ($("body").hasClass("fixed")) {
        $(".content-wrapper, .right-side").css('min-height', window_height - $('.main-footer').outerHeight());
      } else {
        var postSetWidth;
        if (window_height >= sidebar_height) {
          $(".content-wrapper, .right-side").css('min-height', window_height - neg);
          postSetWidth = window_height - neg;
        } else {
          $(".content-wrapper, .right-side").css('min-height', sidebar_height);
          postSetWidth = sidebar_height;
        }

        //Fix for the control sidebar height
        var controlSidebar = $($.AdminLTE.options.controlSidebarOptions.selector);
        if (typeof controlSidebar !== "undefined") {
          if (controlSidebar.height() > postSetWidth)
            $(".content-wrapper, .right-side").css('min-height', controlSidebar.height());
        }

      }
    },
    fixSidebar: function () {
      //Make sure the body tag has the .fixed class
      if (!$("body").hasClass("fixed")) {
        if (typeof $.fn.slimScroll != 'undefined') {
          $(".sidebar").slimScroll({destroy: true}).height("auto");
        }
        return;
      } else if (typeof $.fn.slimScroll == 'undefined' && window.console) {
        window.console.error("Error: the fixed layout requires the slimscroll plugin!");
      }
      //Enable slimscroll for fixed layout
      if ($.AdminLTE.options.sidebarSlimScroll) {
        if (typeof $.fn.slimScroll != 'undefined') {
          //Destroy if it exists
          $(".sidebar").slimScroll({destroy: true}).height("auto");
          //Add slimscroll
          $(".sidebar").slimscroll({
            height: ($(window).height() - $(".main-header").height()) + "px",
            color: "rgba(0,0,0,0.2)",
            size: "3px"
          });
        }
      }
    }
  };

  /* PushMenu()
   * ==========
   * Adds the push menu functionality to the sidebar.
   *
   * @type Function
   * @usage: $.AdminLTE.pushMenu("[data-toggle='offcanvas']")
   */
  $.AdminLTE.pushMenu = {
    activate: function (toggleBtn) {
      //Get the screen sizes
      var screenSizes = $.AdminLTE.options.screenSizes;

      //Enable sidebar toggle
      $(document).on('click', toggleBtn, function (e) {
        e.preventDefault();

        //Enable sidebar push menu
        if ($(window).width() > (screenSizes.sm - 1)) {
          if ($("body").hasClass('sidebar-collapse')) {
            $("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
          } else {
            $("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
          }
        }
        //Handle sidebar push menu for small screens
        else {
          if ($("body").hasClass('sidebar-open')) {
            $("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
          } else {
            $("body").addClass('sidebar-open').trigger('expanded.pushMenu');
          }
        }
      });

      $(".content-wrapper").click(function () {
        //Enable hide menu when clicking on the content-wrapper on small screens
        if ($(window).width() <= (screenSizes.sm - 1) && $("body").hasClass("sidebar-open")) {
          $("body").removeClass('sidebar-open');
        }
      });

      //Enable expand on hover for sidebar mini
      if ($.AdminLTE.options.sidebarExpandOnHover
        || ($('body').hasClass('fixed')
        && $('body').hasClass('sidebar-mini'))) {
        this.expandOnHover();
      }
    },
    expandOnHover: function () {
      var _this = this;
      var screenWidth = $.AdminLTE.options.screenSizes.sm - 1;
      //Expand sidebar on hover
      $('.main-sidebar').hover(function () {
        if ($('body').hasClass('sidebar-mini')
          && $("body").hasClass('sidebar-collapse')
          && $(window).width() > screenWidth) {
          _this.expand();
        }
      }, function () {
        if ($('body').hasClass('sidebar-mini')
          && $('body').hasClass('sidebar-expanded-on-hover')
          && $(window).width() > screenWidth) {
          _this.collapse();
        }
      });
    },
    expand: function () {
      $("body").removeClass('sidebar-collapse').addClass('sidebar-expanded-on-hover');
    },
    collapse: function () {
      if ($('body').hasClass('sidebar-expanded-on-hover')) {
        $('body').removeClass('sidebar-expanded-on-hover').addClass('sidebar-collapse');
      }
    }
  };

  /* Tree()
   * ======
   * Converts the sidebar into a multilevel
   * tree view menu.
   *
   * @type Function
   * @Usage: $.AdminLTE.tree('.sidebar')
   */
  $.AdminLTE.tree = function (menu) {
    var _this = this;
    var animationSpeed = $.AdminLTE.options.animationSpeed;
    $(document).off('click', menu + ' li a')
      .on('click', menu + ' li a', function (e) {
        //Get the clicked link and the next element
        var $this = $(this);
        var checkElement = $this.next();

        //Check if the next element is a menu and is visible
        if ((checkElement.is('.treeview-menu')) && (checkElement.is(':visible')) && (!$('body').hasClass('sidebar-collapse'))) {
          //Close the menu
          checkElement.slideUp(animationSpeed, function () {
            checkElement.removeClass('menu-open');
            //Fix the layout in case the sidebar stretches over the height of the window
            //_this.layout.fix();
          });
          checkElement.parent("li").removeClass("active");
        }
        //If the menu is not visible
        else if ((checkElement.is('.treeview-menu')) && (!checkElement.is(':visible'))) {
          //Get the parent menu
          var parent = $this.parents('ul').first();
          //Close all open menus within the parent
          var ul = parent.find('ul:visible').slideUp(animationSpeed);
          //Remove the menu-open class from the parent
          ul.removeClass('menu-open');
          //Get the parent li
          var parent_li = $this.parent("li");

          //Open the target menu and add the menu-open class
          checkElement.slideDown(animationSpeed, function () {
            //Add the class active to the parent li
            checkElement.addClass('menu-open');
            parent.find('li.active').removeClass('active');
            parent_li.addClass('active');
            //Fix the layout in case the sidebar stretches over the height of the window
            _this.layout.fix();
          });
        }
        //if this isn't a link, prevent the page from being redirected
        if (checkElement.is('.treeview-menu')) {
          e.preventDefault();
        }
      });
  };

  /* ControlSidebar
   * ==============
   * Adds functionality to the right sidebar
   *
   * @type Object
   * @usage $.AdminLTE.controlSidebar.activate(options)
   */
  $.AdminLTE.controlSidebar = {
    //instantiate the object
    activate: function () {
      //Get the object
      var _this = this;
      //Update options
      var o = $.AdminLTE.options.controlSidebarOptions;
      //Get the sidebar
      var sidebar = $(o.selector);
      //The toggle button
      var btn = $(o.toggleBtnSelector);

      //Listen to the click event
      btn.on('click', function (e) {
        e.preventDefault();
        //If the sidebar is not open
        if (!sidebar.hasClass('control-sidebar-open')
          && !$('body').hasClass('control-sidebar-open')) {
          //Open the sidebar
          _this.open(sidebar, o.slide);
        } else {
          _this.close(sidebar, o.slide);
        }
      });

      //If the body has a boxed layout, fix the sidebar bg position
      var bg = $(".control-sidebar-bg");
      _this._fix(bg);

      //If the body has a fixed layout, make the control sidebar fixed
      if ($('body').hasClass('fixed')) {
        _this._fixForFixed(sidebar);
      } else {
        //If the content height is less than the sidebar's height, force max height
        if ($('.content-wrapper, .right-side').height() < sidebar.height()) {
          _this._fixForContent(sidebar);
        }
      }
    },
    //Open the control sidebar
    open: function (sidebar, slide) {
      //Slide over content
      if (slide) {
        sidebar.addClass('control-sidebar-open');
      } else {
        //Push the content by adding the open class to the body instead
        //of the sidebar itself
        $('body').addClass('control-sidebar-open');
      }
    },
    //Close the control sidebar
    close: function (sidebar, slide) {
      if (slide) {
        sidebar.removeClass('control-sidebar-open');
      } else {
        $('body').removeClass('control-sidebar-open');
      }
    },
    _fix: function (sidebar) {
      var _this = this;
      if ($("body").hasClass('layout-boxed')) {
        sidebar.css('position', 'absolute');
        sidebar.height($(".wrapper").height());
        if (_this.hasBindedResize) {
          return;
        }
        $(window).resize(function () {
          _this._fix(sidebar);
        });
        _this.hasBindedResize = true;
      } else {
        sidebar.css({
          'position': 'fixed',
          'height': 'auto'
        });
      }
    },
    _fixForFixed: function (sidebar) {
      sidebar.css({
        'position': 'fixed',
        'max-height': '100%',
        'overflow': 'auto',
        'padding-bottom': '50px'
      });
    },
    _fixForContent: function (sidebar) {
      $(".content-wrapper, .right-side").css('min-height', sidebar.height());
    }
  };

  /* BoxWidget
   * =========
   * BoxWidget is a plugin to handle collapsing and
   * removing boxes from the screen.
   *
   * @type Object
   * @usage $.AdminLTE.boxWidget.activate()
   *        Set all your options in the main $.AdminLTE.options object
   */
  $.AdminLTE.boxWidget = {
    selectors: $.AdminLTE.options.boxWidgetOptions.boxWidgetSelectors,
    icons: $.AdminLTE.options.boxWidgetOptions.boxWidgetIcons,
    animationSpeed: $.AdminLTE.options.animationSpeed,
    activate: function (_box) {
      var _this = this;
      if (!_box) {
        _box = document; // activate all boxes per default
      }
      //Listen for collapse event triggers
      $(_box).on('click', _this.selectors.collapse, function (e) {
        e.preventDefault();
        _this.collapse($(this));
      });

      //Listen for remove event triggers
      $(_box).on('click', _this.selectors.remove, function (e) {
        e.preventDefault();
        _this.remove($(this));
      });
    },
    collapse: function (element) {
      var _this = this;
      //Find the box parent
      var box = element.parents(".box").first();
      //Find the body and the footer
      var box_content = box.find("> .box-body, > .box-footer, > form  >.box-body, > form > .box-footer");
      if (!box.hasClass("collapsed-box")) {
        //Convert minus into plus
        element.children(":first")
          .removeClass(_this.icons.collapse)
          .addClass(_this.icons.open);
        //Hide the content
        box_content.slideUp(_this.animationSpeed, function () {
          box.addClass("collapsed-box");
        });
      } else {
        //Convert plus into minus
        element.children(":first")
          .removeClass(_this.icons.open)
          .addClass(_this.icons.collapse);
        //Show the content
        box_content.slideDown(_this.animationSpeed, function () {
          box.removeClass("collapsed-box");
        });
      }
    },
    remove: function (element) {
      //Find the box parent
      var box = element.parents(".box").first();
      box.slideUp(this.animationSpeed);
    }
  };
}

/* ------------------
 * - Custom Plugins -
 * ------------------
 * All custom plugins are defined below.
 */

/*
 * BOX REFRESH BUTTON
 * ------------------
 * This is a custom plugin to use with the component BOX. It allows you to add
 * a refresh button to the box. It converts the box's state to a loading state.
 *
 * @type plugin
 * @usage $("#box-widget").boxRefresh( options );
 */
(function ($) {

  "use strict";

  $.fn.boxRefresh = function (options) {

    // Render options
    var settings = $.extend({
      //Refresh button selector
      trigger: ".refresh-btn",
      //File source to be loaded (e.g: ajax/src.php)
      source: "",
      //Callbacks
      onLoadStart: function (box) {
        return box;
      }, //Right after the button has been clicked
      onLoadDone: function (box) {
        return box;
      } //When the source has been loaded

    }, options);

    //The overlay
    var overlay = $('<div class="overlay"><div class="fa fa-refresh fa-spin"></div></div>');

    return this.each(function () {
      //if a source is specified
      if (settings.source === "") {
        if (window.console) {
          window.console.log("Please specify a source first - boxRefresh()");
        }
        return;
      }
      //the box
      var box = $(this);
      //the button
      var rBtn = box.find(settings.trigger).first();

      //On trigger click
      rBtn.on('click', function (e) {
        e.preventDefault();
        //Add loading overlay
        start(box);

        //Perform ajax call
        box.find(".box-body").load(settings.source, function () {
          done(box);
        });
      });
    });

    function start(box) {
      //Add overlay and loading img
      box.append(overlay);

      settings.onLoadStart.call(box);
    }

    function done(box) {
      //Remove overlay and loading img
      box.find(overlay).remove();

      settings.onLoadDone.call(box);
    }

  };

})(jQuery);

/*
 * EXPLICIT BOX CONTROLS
 * -----------------------
 * This is a custom plugin to use with the component BOX. It allows you to activate
 * a box inserted in the DOM after the app.js was loaded, toggle and remove box.
 *
 * @type plugin
 * @usage $("#box-widget").activateBox();
 * @usage $("#box-widget").toggleBox();
 * @usage $("#box-widget").removeBox();
 */
(function ($) {

  'use strict';

  $.fn.activateBox = function () {
    $.AdminLTE.boxWidget.activate(this);
  };

  $.fn.toggleBox = function () {
    var button = $($.AdminLTE.boxWidget.selectors.collapse, this);
    $.AdminLTE.boxWidget.collapse(button);
  };

  $.fn.removeBox = function () {
    var button = $($.AdminLTE.boxWidget.selectors.remove, this);
    $.AdminLTE.boxWidget.remove(button);
  };

})(jQuery);

/*
 * TODO LIST CUSTOM PLUGIN
 * -----------------------
 * This plugin depends on iCheck plugin for checkbox and radio inputs
 *
 * @type plugin
 * @usage $("#todo-widget").todolist( options );
 */
(function ($) {

  'use strict';

  $.fn.todolist = function (options) {
    // Render options
    var settings = $.extend({
      //When the user checks the input
      onCheck: function (ele) {
        return ele;
      },
      //When the user unchecks the input
      onUncheck: function (ele) {
        return ele;
      }
    }, options);

    return this.each(function () {

      if (typeof $.fn.iCheck != 'undefined') {
        $('input', this).on('ifChecked', function () {
          var ele = $(this).parents("li").first();
          ele.toggleClass("done");
          settings.onCheck.call(ele);
        });

        $('input', this).on('ifUnchecked', function () {
          var ele = $(this).parents("li").first();
          ele.toggleClass("done");
          settings.onUncheck.call(ele);
        });
      } else {
        $('input', this).on('change', function () {
          var ele = $(this).parents("li").first();
          ele.toggleClass("done");
          if ($('input', ele).is(":checked")) {
            settings.onCheck.call(ele);
          } else {
            settings.onUncheck.call(ele);
          }
        });
      }
    });
  };
}(jQuery));

$.ajaxSetup({
    beforeSend: function(xhr) {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
});

function generateMessage(arrM) {
    var stringConstructor = "test".constructor;
    var arrayConstructor = [].constructor;
    var objectConstructor = {}.constructor;

    var message = '<ul>';
    if (arrM.constructor === stringConstructor) {
        message += '<li>' + arrM + '</li>';
    } else {
        $.each(arrM, function (index, value) {
            message += '<li>' + value + '</li>';
        })
    }

    message += '</ul>';

    return message;
}

function inputErrorMessage(arrM) {
    $.each(arrM, function (index, value) {
        $("." + index).addClass('has-error');
        $("." + index).append('<p class="help-block text-red error-text">'+value+'</p>');
    })
}

function removeInputErrorMessage() {
    $(".has-error").removeClass('has-error');
    $(".error-text").remove();
}

function createPutInput() {
    return '<input type="hidden" name="_method" value="PUT">';
}

function redirectIfUnauthorizedInAdmin(xhr) {
    if (xhr.status == 401) {

        /*REBEL.showErrorMessage('<ul><li>You are not loggedin or your session is expired <br>' +
            'You will redirecting to the Login Page.</li></ul>', 'app-layout', true);*/

        REBEL.smallNotifTemplate('Your session is expired. We will redirect you to login page.', 'body', 'error');

        setTimeout(function () {
            window.location = '/admin/login';
        }, 3000);
    }
}

function getTokenValue() {
    return document.querySelector('#token').getAttribute('content');
}

function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}

var REBEL = REBEL || {};
REBEL = (function () {
    return {
        initialize: function () {
            getModalContent();
            //REBEL.setIcheck();
        },

        inputSlugify: function(elem, result) {
            var _originInput = elem;

            if(_originInput.length) {
                var _originText = _originInput.val(),
                    _slugText = slugify(_originText);

                //console.log(_originText, _slugText);

                $(result).val(_slugText);
            }
        },

        /*setIcheck: function () {
            $('input.icheck').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
            });
        },*/

        smallNotifTemplate: function (message, places, type) {

            places = typeof places === 'undefined' ? 'body' : places;
            type = typeof type === 'undefined' ? 'success' : type;

            var bgColor = '#f9edbe',
                borderColor = '#f0c36d',
                textColor = '#222';

            if(type === 'error') {
                bgColor = '#f36150';
                textColor = '#fff';
                borderColor = 'rgb(107, 32, 29)';
            }

            var template = '<div class="small-notif" style="padding:2px 10px;border:1px solid transparent; font-weight:700;border-color: '+borderColor+';background-color:'+bgColor+';color:'+textColor+';text-align: center;position: fixed;left: 39%;width: 20%;border-radius: 0px 0px 6px 6px;font-size: 12px;min-width: 180px; z-index:1031;" > ' + message + ' </div>';

            $(places).prepend(template);

            return false;
        },

        smallErrorNotif: function (places) {
            return REBEL.smallNotifTemplate('Error process!', places, 'error');
        },
        smallSuccessNotif: function (places) {
            return REBEL.smallNotifTemplate('Success.', places, 'success');
        },


        messageTemplate: function (message, alertType, title, icon, fixed) {
            alertType = typeof alertType !== 'undefined' ? alertType : 'info';

            if (fixed == false || fixed == 'undefined') {
                return '<div class="alert alert-' + alertType + ' alert-dismissable"' +
                    'style="">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                    '<h4 class="small-type"><i class="fa fa-' + icon + '"></i> ' + title + '!</h4>' +
                    message + '</div>';
            }

            return '<div class="alert alert-' + alertType + ' alert-dismissable"' +
                'style="position: fixed; width: 50%;z-index: 8889; margin-left: 25%; margin-top: 5px;">' +
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                '<h4 class="small-type"><i class="fa fa-' + icon + '"></i> ' + title + '!</h4>' +
                message + '</div>';
        },
        scrollToTop: function (element) {
            $(element).scrollTop(0);
        },

        /*showLoading: function (container) {
         var $background = 'background-color: #f3ce85; -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=80); filter: alpha(opacity=80); -moz-opacity: 0.7; -khtml-opacity: 0.7; opacity: 0.7; ',
         $loading = '<div id="loading" style="' + $background +
         'z-index: 999; margin: -15px; width: 100%; height: 100%; position: absolute; overflow: hidden;">' +
         ' <img style="position: absolute; top: 39%; left: 37%; " src="/img/loading.gif"></div>';
         $('#' + container).prepend($loading);

         return false;
         },*/

        hideLoading: function () {
            $("#loading").remove();

            return false;
        },

        removeAllMessageAlert: function () {
            $(".alert").remove();
            $(".small-notif").remove();
            return false;
        },

        showErrorMessage: function (message, tagIdToAdd, fixed) {
            tagIdToAdd = typeof tagIdToAdd !== 'undefined' ? tagIdToAdd : 'body';

            $("." + tagIdToAdd).prepend(REBEL.messageTemplate(message, 'danger', 'Whoops!!! Something wrong with your process.', 'ban', fixed));
        },

        showInfoMessage: function (message, tagIdToAdd, fixed) {
            tagIdToAdd = typeof tagIdToAdd !== 'undefined' ? tagIdToAdd : 'body';

            $("." + tagIdToAdd).prepend(REBEL.messageTemplate(message, 'info', 'Info', 'bullhorn', fixed));
        },

        showWarningMessage: function (message, tagIdToAdd, fixed) {
            tagIdToAdd = typeof tagIdToAdd !== 'undefined' ? tagIdToAdd : 'body';

            $("." + tagIdToAdd).prepend(REBEL.messageTemplate(message, 'warning', 'Warning', 'warning', fixed));
        },

        showSuccessMessage: function (message, tagIdToAdd, fixed) {
            tagIdToAdd = typeof tagIdToAdd !== 'undefined' ? tagIdToAdd : 'body';
            var ele = $("." + tagIdToAdd);
            ele.prepend(REBEL.messageTemplate(message, 'success', 'YayyY... Process successed!', 'thumbs-up', fixed));
        },
        isJson: function (jsonString) {
            try {
                var o = JSON.parse(jsonString);
                if (o && typeof o === "object" && o !== null) {
                    return o;
                }
            }
            catch (e) {
            }

            return false;
        },
        disableElement: function (form) {
            form.find('input, textarea, button, select').attr('disabled', 'disabled');
        },
        enableElement: function (form) {
            setTimeout(function () {
                form.find('input, textarea, button, select').removeAttr('disabled');
            }, 500);
        },
        onSubmit: function (form, callback, closeModal) {
            var _form = form,
                _action = _form.attr('action'),
                _method = $("#action_method").length ? $("#action_method").val() : _form.attr('method'),
                _inputData = _form.serializeJSON();

            //noinspection JSDuplicatedDeclaration
            var closeModal = !!(typeof closeModal == 'undefined' || closeModal != false);

            REBEL.disableElement(_form);
            REBEL.removeAllMessageAlert();
            removeInputErrorMessage();
            $.ajax({
                url: _action,
                type: _method,
                dataType: 'JSON',
                data: _inputData,
                success: function (data, textStatus, requestObject) {
                    if (REBEL.isJson(data)) {
                        data = $.parseJSON(data)
                    }

                    // redirect to login page if authentication session expired.
                    if(requestObject.status === 401) {
                        return redirectIfUnauthorizedInAdmin(requestObject);
                    }

                    // check status of response payloads.
                    if (data.status == 'ok') {
                        //REBEL.showSuccessMessage(generateMessage(data.messages), "modal-body", false)
                        $('.modal-content').length ? REBEL.smallSuccessNotif('.modal-content') : REBEL.smallSuccessNotif('.body');

                        if (callback && typeof(callback) === "function") {
                            REBEL.enableElement(_form);
                            var responseData = data;
                            callback(responseData);
                        }

                        // close modal
                        if (closeModal) {
                            setTimeout(function () {
                                $(".btn-modal-close").trigger("click");
                                $("#modalContent").on('hide.bs.modal', function (e) {
                                    $(this).remove()
                                });
                            }, 2500);
                        } else {
                            setTimeout(function () {
                                REBEL.removeAllMessageAlert();
                            }, 1500);
                        }
                    } else {
                        // do something for error handler.
                        //REBEL.showSuccessMessage(generateMessage(data.messages), "modal-body", false)
                        REBEL.smallErrorNotif('.modal-content');
                    }

                    REBEL.enableElement(_form);
                },
                error: function (requestObject, error, errorThrown) {
                    if(requestObject.status === 401) {
                        return redirectIfUnauthorizedInAdmin(requestObject);
                    }

                    REBEL.enableElement(_form);
                    var _output = JSON.parse(requestObject.responseText);
                    //_output = generateMessage(_output);

                    inputErrorMessage(_output);

                    setTimeout(function () {
                        //REBEL.smallErrorNotif('.modal-content');REBEL.showErrorMessage(_output, "modal-body", false)
                        REBEL.smallErrorNotif('.modal-content');
                    }, 200);
                }
            }).done(function () {
                REBEL.scrollToTop('#modalContent')
            })
        },
        onChangeStatus: function (element, callback) {
            var url = element.attr('href');
            alert(url);

            $.post(url, function (data, status) {
                alert("Data: " + data + "\nStatus: " + status);
            })
        }
    }
})();

function getModalContent() {
    $('a[data-toggle|="modal"]').on('click', function (e) {
        e.preventDefault();
        $('.alert').remove();
        var url = $(this).attr('href');
        var modalSize = $(this).attr('modal-size');
        modalSize = typeof modalSize != 'undefined' ? modalSize : '';

        if (!url.indexOf('#') == 0) {
            var modal, modalDialog, modalContent;
            modal = $('<div id="modalContent" class="modal fade" role="dialog" data-backdrop="static"/>');
            modalDialog = $('<div id="modalContent" class="modal-dialog ' + modalSize + '" />');
            modalContent = $('<div class="modal-content"><div class="modal-loading"></div></div>');

            modal.modal('hide')
                .append(modalDialog)
                .on('hidden.bs.modal', function () {
                    $(this).remove();
                })
                .appendTo('body');

            modalDialog.append(modalContent);
            modal.modal('show');

            // check for delete dialog
            var deleteAttr = $(this).attr('for-delete');
            if (typeof deleteAttr !== typeof undefined && deleteAttr !== false) {
                var dataMessage = $(this).attr('data-message');
                var token = getTokenValue();
                var deleteHtml = '<form method="POST" action="' + url + '">';
                deleteHtml += '<div class="modal-header"><a class="close" data-dismiss="modal">&times;</a><h4><i class="fa fa-tag fa-btn"></i> Delete Confirmation</h4></div>';
                deleteHtml += '<div class="modal-body">'
                deleteHtml += '<input type="hidden" name="_token" value="' + token + '">';
                deleteHtml += '<input type="hidden" id="action_method" class="action_method" name="_method" value="DELETE">';
                deleteHtml += '<p>' + dataMessage + '</p>';
                deleteHtml += '</div>';
                deleteHtml += '<div class="modal-footer"><i class="fa fa-floppy-ofa-btn"></i><button class="btn btn-danger"><i class="fa fa-trash fa-btn"></i> <span class="ladda-label">Delete!</span></button><a class="btn btn-modal-close" data-dismiss="modal">Close</a></div>';
                deleteHtml += '</form>';

                modalContent.html(deleteHtml);

            } else {
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function (data) {
                        modalContent.html(data);
                    },
                    error: function (requestObject, error, errorThrown) {
                        var modal = $("#modalContent");
                        modal.modal('hide');
                        modal.on('hide.bs.modal', function (e) {
                            $(this).remove()
                        });
                        //console.log(requestObject, error, errorThrown);

                        if(requestObject.status === 401) {
                            return redirectIfUnauthorizedInAdmin(requestObject);
                        }

                        var _output = JSON.parse(requestObject.responseText);
                        _output = generateMessage(_output);

                        REBEL.showErrorMessage(_output, 'body', true);
                    }
                });
            }
        }
    });
}

$(document).ready(function () {
    REBEL.initialize();
});

//# sourceMappingURL=admin.js.map
