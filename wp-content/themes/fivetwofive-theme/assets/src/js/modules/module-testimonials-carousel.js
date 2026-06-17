/**
 * Testimonials carousel module.
 *
 * Initialises the Swiper carousel. Swiper has no jQuery dependency, so this
 * runs as a plain vanilla module.
 */
( function() {
	'use strict';

	const init = () => {
		if ( typeof Swiper === 'undefined' ) {
			return;
		}

		const carousels = document.querySelectorAll(
			'.ftf-module-testimonials-carousel .swiper-container'
		);

		carousels.forEach( ( carousel ) => {
			// eslint-disable-next-line no-new
			new Swiper( carousel, {
				loop: true,
				pagination: {
					el: carousel.querySelector( '.swiper-pagination' ),
					clickable: true,
				},
				navigation: {
					nextEl: carousel.querySelector( '.swiper-button-next' ),
					prevEl: carousel.querySelector( '.swiper-button-prev' ),
				},
			} );
		} );
	};

	document.addEventListener( 'DOMContentLoaded', init );
}() );
