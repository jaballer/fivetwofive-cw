( function( $ ) {
	'use strict';

	const announcementModule = ( function() {
		const makeSticky = function() {
			if ( $( '.ftf-module-announcement' ).hasClass( 'js-is-sticky-yes' ) ) {
				const height = $( '.ftf-module-announcement' ).outerHeight( true );
				$( '.ftf-module-announcement' ).wrap( '<div class="sticky-announcement-spacer"></div>' );
				$( '.sticky-announcement-spacer' ).css( {
					height,
				} );
				$( 'body' ).prepend( $( '.sticky-announcement-spacer' ) );
			}
		};

		const closeModule = function() {
			$( '.ftf-module-announcement__close' ).on( 'click', function( e ) {
				e.preventDefault();
				$( this ).parent( '.ftf-module-announcement' ).slideUp( 400 );

				if ( $( '.sticky-announcement-spacer' ).length ) {
					$( '.sticky-announcement-spacer' ).slideUp( 400 );
				}
			} );
		};

		function init() {
			makeSticky();
			closeModule();
		}

		return {
			init,
		};
	}() );

	$( function() {
		announcementModule.init();
	} );

}( jQuery ) );
