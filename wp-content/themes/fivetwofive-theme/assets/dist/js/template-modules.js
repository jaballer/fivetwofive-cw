"use strict";

/**
 * Module template behaviour.
 *
 * Applies ScrollReveal animations to modules flagged with `.ftf-module-hidden`.
 * Converted from jQuery to vanilla JS (no jQuery dependency).
 */
(function (d) {
  'use strict';

  var init = function init() {
    var animatedModules = d.querySelectorAll('.ftf-module-hidden');
    if (!animatedModules.length || typeof ScrollReveal === 'undefined') {
      return;
    }
    animatedModules.forEach(function (animatedModule) {
      try {
        ScrollReveal().reveal('#' + animatedModule.id, JSON.parse(animatedModule.dataset.animation));
      } catch (e) {
        // eslint-disable-next-line no-console
        console.error('FiveTwoFive: Invalid animation data on #' + animatedModule.id, e);
      }
    });
  };
  d.addEventListener('DOMContentLoaded', init);
})(document);