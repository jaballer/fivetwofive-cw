"use strict";

(function () {
  'use strict';

  var workModule = function () {
    var init = function init(module) {
      if (hasForm(module)) {
        searchFilterInit(module);
      }
    };
    var hasForm = function hasForm(module) {
      return null !== module.querySelector('.ftf-form');
    };
    var generateTokens = function generateTokens(module) {
      var items = module.querySelectorAll('.ftf_work');
      var tokens = [];
      items.forEach(function (item) {
        var _item$querySelector$t, _item$querySelector;
        var itemTermLinks = item.querySelectorAll('.card__categories a');
        var itemTermIds = [];
        itemTermLinks.forEach(function (link) {
          itemTermIds.push(parseInt(link.dataset.id, 10));
        });
        tokens.push({
          id: item.id,
          title: (_item$querySelector$t = (_item$querySelector = item.querySelector('.card__title')) === null || _item$querySelector === void 0 ? void 0 : _item$querySelector.textContent.toLowerCase()) !== null && _item$querySelector$t !== void 0 ? _item$querySelector$t : '',
          terms: itemTermIds
        });
      });
      return tokens;
    };
    var searchFilterInit = function searchFilterInit(module) {
      module.querySelector('.ftf-form').addEventListener('submit', function (e) {
        e.preventDefault();
        var search = e.currentTarget.querySelector('input[type="search"]').value.trim().toLowerCase();
        var term = parseInt(e.currentTarget.querySelector('select[name="ftf-work-category"]').value, 10);
        hideItems(module.querySelectorAll('.ftf_work'));
        var filteredWorks = filterWorks(search, term, module);
        if (filteredWorks.length > 0) {
          hideEmptyMessage(module);
          animateItems(filteredWorks, module);
        } else {
          showEmptyMessage(module);
        }
      });
    };
    var filterWorks = function filterWorks(search, term, module) {
      var filteredWorks = generateTokens(module);
      if ('' !== search) {
        filteredWorks = filteredWorks.filter(function (token) {
          return token.title.includes(search);
        });
      }
      if (0 !== term) {
        filteredWorks = filteredWorks.filter(function (token) {
          return token.terms.includes(term);
        });
      }
      return filteredWorks.map(function (item) {
        return document.getElementById(item.id);
      }).filter(Boolean);
    };
    var hideItems = function hideItems(items) {
      items.forEach(function (item) {
        item.style.display = 'none';
        item.style.opacity = '';
        item.classList.remove('active');
      });
    };
    var animateItems = function animateItems(items, module) {
      items.forEach(function (item) {
        item.classList.add('active');
      });
      module.querySelectorAll('.ftf_work.active').forEach(function (item, i) {
        setTimeout(function () {
          item.style.opacity = '0';
          item.style.display = '';
          // Trigger reflow so the transition fires from opacity 0.
          item.getBoundingClientRect();
          item.style.opacity = '1';
        }, 300 * i);
      });
    };
    var showEmptyMessage = function showEmptyMessage(module) {
      var msg = module.querySelector('.ftf-module-works__empty-results');
      if (msg) {
        msg.style.display = '';
      }
    };
    var hideEmptyMessage = function hideEmptyMessage(module) {
      var msg = module.querySelector('.ftf-module-works__empty-results');
      if (msg) {
        msg.style.display = 'none';
      }
    };
    return {
      init: init
    };
  }();
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ftf-module-works').forEach(function (module) {
      workModule.init(module);
    });
  });
})();