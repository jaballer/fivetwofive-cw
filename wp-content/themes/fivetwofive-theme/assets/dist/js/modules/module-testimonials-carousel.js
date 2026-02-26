"use strict";

(function ($) {
  'use strict';

  var testimonialCarouselModule = function () {
    var init = function init() {
      if (typeof Swiper === 'undefined') {
        return;
      }
      new Swiper('.ftf-module-testimonials-carousel .swiper-container', {
        loop: true,
        pagination: {
          el: '.swiper-pagination',
          clickable: true
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev'
        }
      });
    };
    return {
      init: init
    };
  }();
  $(function () {
    testimonialCarouselModule.init();
  });
})(jQuery);