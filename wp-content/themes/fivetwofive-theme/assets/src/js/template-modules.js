( function( $, d ) {
	'use strict';

	$( function() {
		const animatedModules = d.querySelectorAll( '.ftf-module-hidden' );
		if ( animatedModules.length > 0 ) {
			animatedModules.forEach( function( animatedModule ) {
				try {
					ScrollReveal().reveal( '#' + animatedModule.id, JSON.parse( animatedModule.dataset.animation ) );
				} catch ( e ) {
					// eslint-disable-next-line no-console
					console.error( 'FiveTwoFive: Invalid animation data on #' + animatedModule.id, e );
				}
			} );
		}
	} );
}( jQuery, document ) );
