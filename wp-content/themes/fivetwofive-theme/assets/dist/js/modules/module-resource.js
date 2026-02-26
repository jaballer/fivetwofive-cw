"use strict";

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
(function (FTF, ScrollReveal) {
  'use strict';

  var resourceModule = /*#__PURE__*/function () {
    function resourceModule(module) {
      _classCallCheck(this, resourceModule);
      this.module = module;
      this.itemPerPage = module.dataset.itemPerPage;
      this.paginationContainer = module.querySelector('.ftf-module__pagination-container');
      this.statusRegion = this.createStatusRegion();
      this.animationConfig = {
        mobile: false,
        duration: 1000,
        interval: 300,
        reset: false,
        distance: '10px'
      };
    }
    return _createClass(resourceModule, [{
      key: "createStatusRegion",
      value: function createStatusRegion() {
        var region = document.createElement('p');
        region.setAttribute('aria-live', 'polite');
        region.setAttribute('aria-atomic', 'true');
        region.classList.add('screen-reader-text');
        this.module.insertAdjacentElement('afterbegin', region);
        return region;
      }
    }, {
      key: "init",
      value: function init() {
        this.formInit();
        this.paginationInit();
      }
    }, {
      key: "animateResources",
      value: function animateResources() {
        ScrollReveal().reveal(this.module.querySelectorAll('.card'), this.animationConfig);
      }
    }, {
      key: "formInit",
      value: function formInit() {
        var _this = this;
        this.module.querySelector('.ftf-form').addEventListener('submit', function (event) {
          event.preventDefault();
          _this.fetchResources(1);
        });
      }
    }, {
      key: "fetchResources",
      value: function fetchResources(page) {
        var _this$module$querySel,
          _this$module$querySel2,
          _this2 = this;
        var requestURL = new URL(FTF.restBase);
        requestURL.searchParams.append('_fields', 'id,date_gmt,ftf_formatted_date,ftf_resource_categories,ftf_resource_tags,title,link,_links,_embedded');
        requestURL.searchParams.append('per_page', this.itemPerPage);
        requestURL.searchParams.append('page', page);
        requestURL.searchParams.append('_embed', 'wp:featuredmedia');
        var search = this.module.querySelector('[name="ftf-search-resource"]').value;
        var category = (_this$module$querySel = (_this$module$querySel2 = this.module.querySelector('[name="ftf-category-resource"]')) === null || _this$module$querySel2 === void 0 ? void 0 : _this$module$querySel2.value) !== null && _this$module$querySel !== void 0 ? _this$module$querySel : null;
        if (search) {
          requestURL.searchParams.append('search', search);
        }
        if (category && category !== '0') {
          requestURL.searchParams.append('ftf-resource-categories', category);
        }
        this.isLoading();
        fetch(requestURL.href).then(function (response) {
          if (!response.ok) {
            throw new Error("HTTP ".concat(response.status));
          }
          var totalPages = response.headers.get('X-WP-TotalPages');
          return response.json().then(function (data) {
            return {
              data: data,
              totalPages: totalPages
            };
          });
        }).then(function (_ref) {
          var data = _ref.data,
            totalPages = _ref.totalPages;
          _this2.updateResources(data);
          _this2.generatePagination(totalPages);
        })["catch"](function (error) {
          // eslint-disable-next-line no-console
          console.error('FiveTwoFive: Error fetching resources', error);
          _this2.statusRegion.textContent = 'Error loading resources. Please try again.';
        })["finally"](function () {
          _this2.isComplete();
        });
      }
    }, {
      key: "updateResources",
      value: function updateResources(data) {
        var _this3 = this;
        var resourcesWrap = this.module.querySelector('.ftf-resources-wrap');
        resourcesWrap.innerHTML = '';
        data.forEach(function (resource) {
          resourcesWrap.insertAdjacentHTML('beforeend', _this3.createResource(resource));
        });
        this.animateResources();
        var count = data.length;
        this.statusRegion.textContent = count ? "".concat(count, " resource").concat(count !== 1 ? 's' : '', " loaded.") : 'No resources found.';
      }
    }, {
      key: "createResource",
      value: function createResource(resource) {
        var resourceHTML = '';
        var categories = '';
        var tags = '';
        var image = '';
        if (resource) {
          var _resource$_embedded;
          if (resource !== null && resource !== void 0 && resource.ftf_resource_categories) {
            categories += '<ul class="card__categories">';
            resource.ftf_resource_categories.forEach(function (category) {
              categories += "<li><a href=\"".concat(category.link, "\">").concat(category.name, "</a></li>");
            });
            categories += '</ul>';
          }
          if (resource !== null && resource !== void 0 && resource.ftf_resource_tags && resource.ftf_resource_tags.length > 0) {
            tags += '<p class="card__tags"><strong>Tags:</strong> ';
            resource.ftf_resource_tags.forEach(function (tag, i) {
              if (i !== 0) {
                tags += ',';
              }
              tags += " <a rel=\"tag\" href=\"".concat(tag.link, "\">").concat(tag.name, "</a>");
            });
            tags += '</p>';
          }
          if ((_resource$_embedded = resource._embedded) !== null && _resource$_embedded !== void 0 && (_resource$_embedded = _resource$_embedded['wp:featuredmedia']) !== null && _resource$_embedded !== void 0 && (_resource$_embedded = _resource$_embedded[0]) !== null && _resource$_embedded !== void 0 && (_resource$_embedded = _resource$_embedded.media_details) !== null && _resource$_embedded !== void 0 && (_resource$_embedded = _resource$_embedded.sizes) !== null && _resource$_embedded !== void 0 && (_resource$_embedded = _resource$_embedded['ftf-resource-thumb']) !== null && _resource$_embedded !== void 0 && _resource$_embedded.source_url) {
            image += "<img width=\"415\" height=\"245\" src=\"".concat(resource._embedded['wp:featuredmedia'][0].media_details.sizes['ftf-resource-thumb'].source_url, "\" class=\"card__image img-responsive wp-post-image\" alt=\"").concat(resource.title.rendered, "\" loading=\"lazy\">");
          }
          resourceHTML = "\n\t\t\t\t  <div class=\"col-md-4 mb-3 mb-md-5\">\n\t\t\t\t\t<article id=\"card-".concat(resource.id, "\" class=\"card post-2990 ftf_resource type-ftf_resource status-publish has-post-thumbnail hentry load-hidden\">\n\t\t\t\t\t\t<div class=\"card__top\">\n\t\t\t\t\t\t\t").concat(categories, "\n\t\t\t\t\t\t\t").concat(image, "\n\t\t\t\t\t\t</div>\n\t\t\t\t\t  <div class=\"card__bottom\">\n\t\t\t\t\t\t<header class=\"card__header m-0\">\n\t\t\t\t\t\t  <div class=\"ftf-post-meta entry-meta\"><span class=\"posted-on\"><a href=\"").concat(resource.link, "\" rel=\"bookmark\"><time class=\"entry-date published\" datetime=\"").concat(resource.date, "\">").concat(resource.ftf_formatted_date, "</time></a></span></div>\n\t\t\t\t\t\t  <h3 class=\"card__title mt-2\"><a href=\"").concat(resource.link, "\">").concat(resource.title.rendered, "</a></h3>\n\t\t\t\t\t\t  ").concat(tags, "\n\t\t\t\t\t\t</header>\n\t\t\t\t\t  </div>\n\t\t\t\t\t</article>\n\t\t\t\t  </div>\n\t\t\t\t");
        }
        return resourceHTML;
      }
    }, {
      key: "generatePagination",
      value: function generatePagination(totalPages) {
        this.paginationContainer.innerHTML = '';
        if (totalPages <= 1) {
          return;
        }
        var currentPage = this.module.dataset.currentPage;
        var paginationNav = this.setupPaginationNav();
        paginationNav.querySelector('.nav-links').innerHTML = this.generatePaginationLinks(currentPage, totalPages);
        this.paginationContainer.insertAdjacentElement('beforeend', paginationNav);
      }
    }, {
      key: "setupPaginationNav",
      value: function setupPaginationNav() {
        var paginationNav = document.createElement('nav');
        paginationNav.classList.add('navigation', 'pagination');
        paginationNav.setAttribute('role', 'navigation');
        paginationNav.setAttribute('aria-label', 'Resources');
        var paginationHeading = document.createElement('h2');
        paginationHeading.classList.add('screen-reader-text');
        paginationHeading.textContent = 'Resources navigation';
        var paginationLinks = document.createElement('div');
        paginationLinks.classList.add('nav-links');
        paginationNav.insertAdjacentElement('afterbegin', paginationHeading);
        paginationNav.insertAdjacentElement('beforeend', paginationLinks);
        return paginationNav;
      }
    }, {
      key: "generatePaginationLinks",
      value: function generatePaginationLinks() {
        var current = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
        var totalPages = arguments.length > 1 ? arguments[1] : undefined;
        var paginationLinks = '';
        current = Number.parseInt(current, 10);
        for (var index = 1; index <= totalPages; index++) {
          if (index === current) {
            paginationLinks += "<span aria-current=\"page\" class=\"page-numbers current\">".concat(index, "</span>");
          } else {
            paginationLinks += "<a class=\"page-numbers\" aria-label=\"Go to page ".concat(index, "\" data-page=\"").concat(index, "\" href=\"#\">").concat(index, "</a>");
          }
        }
        return paginationLinks;
      }
    }, {
      key: "paginationInit",
      value: function paginationInit() {
        var _this4 = this;
        this.paginationContainer.addEventListener('click', function (event) {
          var link = event.target.closest('.page-numbers[data-page]');
          if (!link) {
            return;
          }
          event.preventDefault();
          var currentPage = Number.parseInt(link.dataset.page, 10);
          _this4.module.dataset.currentPage = currentPage;
          _this4.fetchResources(currentPage);
        });
      }
    }, {
      key: "generateSpinner",
      value: function generateSpinner() {
        return "<div class=\"fivetwofive-spinner\"><div></div><div></div></div>";
      }
    }, {
      key: "isLoading",
      value: function isLoading() {
        var resourcesWrap = this.module.querySelector('.ftf-resources-wrap');
        this.module.querySelector('input[type="submit"]').setAttribute('disabled', 'disabled');
        resourcesWrap.innerHTML = this.generateSpinner();
        this.statusRegion.textContent = 'Loading resources...';
      }
    }, {
      key: "isComplete",
      value: function isComplete() {
        this.module.querySelector('input[type="submit"]').removeAttribute('disabled');
      }
    }]);
  }();
  document.addEventListener('DOMContentLoaded', function () {
    var modules = document.querySelectorAll('.ftf-module-resources');
    modules.forEach(function (module) {
      var singleResourceModule = new resourceModule(module);
      singleResourceModule.init();
    });
  });
})(FiveTwoFive, ScrollReveal);