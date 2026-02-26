"use strict";

(function ($) {
  'use strict';

  var accordionModule = function () {
    function init() {
      $('.ftf-module-accordion').each(function (i, module) {
        var accordionModuleInstance = $(module);
        accordionModuleInstance.find('.ftf-module-accordion__panels').accordion({
          header: '.ftf-module-accordion__panel-title',
          collapsible: true,
          active: false,
          icons: {
            header: 'ftf-module-accordion-icon-default',
            activeHeader: 'ftf-module-accordion-icon-active'
          }
        });
      });
    }
    return {
      init: init
    };
  }();
  $(function () {
    accordionModule.init();
  });

  // eslint-disable-next-line no-undef
})(jQuery);