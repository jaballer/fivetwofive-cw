/**
 * Announcement module.
 *
 * Handles the optional sticky behaviour and the dismiss interaction for the
 * announcement bar. Converted from jQuery to vanilla JS (no jQuery dependency).
 */
( function() {
	'use strict';

	const SELECTOR = '.ftf-module-announcement';
	const SPACER_CLASS = 'sticky-announcement-spacer';
	const DURATION = 400;

	/**
	 * Collapse and hide an element with a height transition, mirroring
	 * jQuery's slideUp().
	 *
	 * @param {HTMLElement} el       Element to slide up.
	 * @param {number}      duration Animation duration in ms.
	 */
	const slideUp = ( el, duration = DURATION ) => {
		el.style.overflow = 'hidden';
		el.style.height = el.offsetHeight + 'px';
		el.style.transition = `height ${ duration }ms ease, padding ${ duration }ms ease, margin ${ duration }ms ease`;

		// Force a reflow so the starting height is committed before collapsing.
		void el.offsetHeight;

		el.style.height = '0';
		el.style.paddingTop = '0';
		el.style.paddingBottom = '0';
		el.style.marginTop = '0';
		el.style.marginBottom = '0';

		window.setTimeout( () => {
			el.style.display = 'none';
		}, duration );
	};

	/**
	 * Reserve layout space for a sticky announcement by wrapping it in a
	 * spacer of the same height and moving it to the top of the document body.
	 */
	const makeSticky = () => {
		const announcement = document.querySelector( SELECTOR );

		if ( ! announcement || ! announcement.classList.contains( 'js-is-sticky-yes' ) ) {
			return;
		}

		const styles = window.getComputedStyle( announcement );
		const height = announcement.offsetHeight +
			parseFloat( styles.marginTop ) +
			parseFloat( styles.marginBottom );

		const spacer = document.createElement( 'div' );
		spacer.className = SPACER_CLASS;
		spacer.style.height = height + 'px';

		announcement.parentNode.insertBefore( spacer, announcement );
		spacer.appendChild( announcement );
		document.body.prepend( spacer );
	};

	/**
	 * Wire up the dismiss button.
	 */
	const closeModule = () => {
		const closeButton = document.querySelector( '.ftf-module-announcement__close' );

		if ( ! closeButton ) {
			return;
		}

		closeButton.addEventListener( 'click', ( e ) => {
			e.preventDefault();

			const announcement = e.currentTarget.closest( SELECTOR );
			if ( announcement ) {
				slideUp( announcement );
			}

			const spacer = document.querySelector( '.' + SPACER_CLASS );
			if ( spacer ) {
				slideUp( spacer );
			}
		} );
	};

	document.addEventListener( 'DOMContentLoaded', () => {
		makeSticky();
		closeModule();
	} );
}() );
