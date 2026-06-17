"use strict";

/**
 * Announcement module.
 *
 * Handles the optional sticky behaviour and the dismiss interaction for the
 * announcement bar. Converted from jQuery to vanilla JS (no jQuery dependency).
 */
(function () {
  'use strict';

  var SELECTOR = '.ftf-module-announcement';
  var SPACER_CLASS = 'sticky-announcement-spacer';
  var DURATION = 400;

  /**
   * Collapse and hide an element with a height transition, mirroring
   * jQuery's slideUp().
   *
   * @param {HTMLElement} el       Element to slide up.
   * @param {number}      duration Animation duration in ms.
   */
  var slideUp = function slideUp(el) {
    var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : DURATION;
    el.style.overflow = 'hidden';
    el.style.height = el.offsetHeight + 'px';
    el.style.transition = "height ".concat(duration, "ms ease, padding ").concat(duration, "ms ease, margin ").concat(duration, "ms ease");

    // Force a reflow so the starting height is committed before collapsing.
    void el.offsetHeight;
    el.style.height = '0';
    el.style.paddingTop = '0';
    el.style.paddingBottom = '0';
    el.style.marginTop = '0';
    el.style.marginBottom = '0';
    window.setTimeout(function () {
      el.style.display = 'none';
    }, duration);
  };

  /**
   * Reserve layout space for each sticky announcement by wrapping it in a
   * spacer of the same height and moving it to the top of the document body.
   *
   * A page can render more than one announcement module, so every matching
   * instance is handled, not just the first.
   */
  var makeSticky = function makeSticky() {
    var announcements = document.querySelectorAll(SELECTOR);
    announcements.forEach(function (announcement) {
      if (!announcement.classList.contains('js-is-sticky-yes')) {
        return;
      }
      var styles = window.getComputedStyle(announcement);
      var height = announcement.offsetHeight + parseFloat(styles.marginTop) + parseFloat(styles.marginBottom);
      var spacer = document.createElement('div');
      spacer.className = SPACER_CLASS;
      spacer.style.height = height + 'px';
      announcement.parentNode.insertBefore(spacer, announcement);
      spacer.appendChild(announcement);
      document.body.prepend(spacer);
    });
  };

  /**
   * Wire up the dismiss button on every announcement module.
   */
  var closeModule = function closeModule() {
    var closeButtons = document.querySelectorAll('.ftf-module-announcement__close');
    closeButtons.forEach(function (closeButton) {
      closeButton.addEventListener('click', function (e) {
        e.preventDefault();

        // When the announcement is sticky it lives inside its spacer, so
        // collapse the spacer (which contains the announcement). Otherwise
        // collapse the announcement itself.
        var spacer = e.currentTarget.closest('.' + SPACER_CLASS);
        var announcement = e.currentTarget.closest(SELECTOR);
        if (spacer) {
          slideUp(spacer);
        } else if (announcement) {
          slideUp(announcement);
        }
      });
    });
  };
  document.addEventListener('DOMContentLoaded', function () {
    makeSticky();
    closeModule();
  });
})();