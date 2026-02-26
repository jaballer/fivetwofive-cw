"use strict";

/**
 * Handle blog post view toggle between grid and list views
 */
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var viewToggleButtons = document.querySelectorAll('.view-toggle__btn');
    var postsContainer = document.querySelector('.posts-container');
    if (!postsContainer || !viewToggleButtons.length) return;

    // Get the saved view preference from localStorage
    var savedView = localStorage.getItem('blogViewPreference') || 'grid';

    // Apply the saved view preference
    if (savedView === 'list') {
      postsContainer.classList.remove('view-grid');
      postsContainer.classList.add('view-list');
      document.querySelector('.view-toggle__btn--list').classList.add('active');
      document.querySelector('.view-toggle__btn--grid').classList.remove('active');
    }
    viewToggleButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        var view = this.dataset.view;

        // Remove active class from all buttons
        viewToggleButtons.forEach(function (btn) {
          return btn.classList.remove('active');
        });

        // Add active class to clicked button
        this.classList.add('active');

        // Update container class
        postsContainer.classList.remove('view-grid', 'view-list');
        postsContainer.classList.add('view-' + view);

        // Save preference to localStorage
        localStorage.setItem('blogViewPreference', view);
      });
    });
  });
})();