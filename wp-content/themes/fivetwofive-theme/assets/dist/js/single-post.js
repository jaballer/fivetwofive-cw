"use strict";

/**
 * Single Post Social Tooltip Enhancement
 * 
 * This module adds interactive social sharing tooltips to highlighted text
 * (marked with <mark> tags) in post content. When users hover over highlighted
 * text, a Twitter sharing tooltip appears with a pre-filled tweet.
 * 
 * Dependencies:
 * - Popper.js library (for tooltip positioning)
 * - DOM elements with class 'entry-content' containing <mark> tags
 * 
 * @author Development Team
 * @version 1.0.0
 */

(function () {
  // Wait for DOM to be fully loaded before initializing
  document.addEventListener('DOMContentLoaded', function () {
    // Find all highlighted text elements within post content
    var highlightedTexts = document.querySelectorAll('.entry-content mark');

    // Twitter SVG icon for the sharing button
    // Note: This is the classic Twitter bird icon (pre-X rebrand)
    var twitterSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" viewBox="0 0 32 32"><path d="M32 7.075a12.941 12.941 0 0 1-3.769 1.031 6.601 6.601 0 0 0 2.887-3.631 13.21 13.21 0 0 1-4.169 1.594A6.565 6.565 0 0 0 22.155 4a6.563 6.563 0 0 0-6.563 6.563c0 .512.056 1.012.169 1.494A18.635 18.635 0 0 1 2.23 5.195a6.56 6.56 0 0 0-.887 3.3 6.557 6.557 0 0 0 2.919 5.463 6.565 6.565 0 0 1-2.975-.819v.081a6.565 6.565 0 0 0 5.269 6.437 6.574 6.574 0 0 1-2.968.112 6.588 6.588 0 0 0 6.131 4.563 13.17 13.17 0 0 1-9.725 2.719 18.568 18.568 0 0 0 10.069 2.95c12.075 0 18.681-10.006 18.681-18.681 0-.287-.006-.569-.019-.85A13.216 13.216 0 0 0 32 7.076z"/></svg>';

    // Store Popper instance for tooltip positioning
    // This will be created/destroyed on each hover event
    var popperInstance;

    /**
     * Creates HTML template for the social sharing tooltip
     * 
     * @param {string} socialText - The highlighted text to be shared
     * @returns {string} HTML template string for the tooltip
     */
    var createSocialTooltip = function createSocialTooltip(socialText) {
      // Build Twitter intent URL with encoded text and current page URL
      // This pre-fills the tweet with the highlighted text and article URL
      var tooltipTemplate = "\n\t\t\t\t<span id=\"socialToolTip\" class=\"tooltip\">\n\t\t\t\t\t<a href=\"https://twitter.com/intent/tweet?text=".concat(encodeURIComponent(socialText), "&url=").concat(encodeURIComponent(window.location.href), "\" target=\"_blank\">").concat(twitterSVG, " Article highlight</a>\n\t\t\t\t</span>\t\n\t\t\t");
      return tooltipTemplate;
    };

    /**
     * Attaches the tooltip HTML to the DOM and returns the tooltip element
     * 
     * @param {HTMLElement} elem - The highlighted text element
     * @param {string} tooltip - HTML string for the tooltip
     * @returns {HTMLElement} The created tooltip element
     */
    var attachTooltip = function attachTooltip(elem, tooltip) {
      // Insert tooltip HTML after the highlighted text element
      elem.insertAdjacentHTML('beforeend', tooltip);
      // Return the newly created tooltip element for further manipulation
      return document.querySelector('#socialToolTip');
    };

    /**
     * Shows the tooltip by adding the data-show attribute
     * This attribute is used by CSS to control tooltip visibility
     * 
     * @param {HTMLElement} tooltip - The tooltip element to show
     */
    var showTooltip = function showTooltip(tooltip) {
      tooltip.setAttribute('data-show', '');
    };

    /**
     * Initializes the social tooltip for a highlighted text element
     * Creates the tooltip, sets up Popper positioning, and shows it
     * 
     * @param {HTMLElement} elem - The highlighted text element to attach tooltip to
     */
    var initializeSocialTooltip = function initializeSocialTooltip(elem) {
      // Create and attach the tooltip HTML to the element
      var tooltip = attachTooltip(elem, createSocialTooltip(elem.textContent));

      // Initialize Popper.js for smart tooltip positioning
      // This handles collision detection and optimal placement
      popperInstance = Popper.createPopper(elem, tooltip, {
        placement: 'top' // Try to place tooltip above the element
      });

      // Make the tooltip visible
      showTooltip(tooltip);
    };

    /**
     * Binds mouse events to all highlighted text elements
     * Sets up hover behavior to show/hide social sharing tooltips
     */
    var bindMarkEvents = function bindMarkEvents() {
      // Early return if no highlighted text elements found
      if (highlightedTexts.length <= 0) {
        return;
      }

      // Add event listeners to each highlighted text element
      highlightedTexts.forEach(function (highlightedText) {
        // Show tooltip on mouse enter
        highlightedText.addEventListener('mouseenter', function (e) {
          initializeSocialTooltip(e.currentTarget);
        });

        // Clean up tooltip on mouse leave
        highlightedText.addEventListener('mouseleave', function () {
          // Destroy Popper instance to free up memory
          popperInstance.destroy();
          // Remove tooltip element from DOM
          document.querySelector('#socialToolTip').remove();
        });
      });
    };

    // Initialize the tooltip functionality
    bindMarkEvents();
  });
})();