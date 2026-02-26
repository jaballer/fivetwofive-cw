"use strict";

(function ($) {
  'use strict';

  var announcementModule = function () {
    var makeSticky = function makeSticky() {
      if ($('.ftf-module-announcement').hasClass('js-is-sticky-yes')) {
        var height = $('.ftf-module-announcement').outerHeight(true);
        $('.ftf-module-announcement').wrap('<div class="sticky-announcement-spacer"></div>');
        $('.sticky-announcement-spacer').css({
          height: height
        });
        $('body').prepend($('.sticky-announcement-spacer'));
      }
    };
    var closeModule = function closeModule() {
      $('.ftf-module-announcement__close').on('click', function (e) {
        e.preventDefault();
        $(this).parent('.ftf-module-announcement').slideUp(400);
        if ($('.sticky-announcement-spacer').length) {
          $('.sticky-announcement-spacer').slideUp(400);
        }
      });
    };
    function init() {
      makeSticky();
      closeModule();
    }
    return {
      init: init
    };
  }();
  $(function () {
    announcementModule.init();
  });
})(jQuery);