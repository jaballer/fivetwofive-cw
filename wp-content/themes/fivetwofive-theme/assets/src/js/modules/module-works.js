( function( $ ) {
	'use strict';

	const workModule = ( () => {
		const init = ( module ) => {
			if ( hasForm( module ) ) {
				searchFilterInit( module );
			}
		};

		const hasForm = ( module ) => {
			return module.find( '.ftf-form' ).length > 0;
		};

		const generateTokens = ( module ) => {
			const items = module.find( '.ftf_work' );
			const tokens = [];

			if ( items.length > 0 ) {
				items.each( function() {
					const item = $( this );

					const itemId = item.attr( 'id' );
					const itemTermLinks = item.find( '.card__categories a' );
					const itemTitle = item.find( '.card__title' ).text().toLowerCase();
					const itemTermIds = [];

					if ( itemTermLinks.length > 0 ) {
						itemTermLinks.each( function() {
							itemTermIds.push( parseInt( $( this ).attr( 'data-id' ), 10 ) );
						} );
					}

					tokens.push( {
						id: itemId,
						title: itemTitle,
						terms: itemTermIds,
					} );
				} );
			}

			return tokens;
		};

		const searchFilterInit = ( module ) => {
			const searchForm = module.find( '.ftf-form' );

			searchForm.on( 'submit', function( e ) {
				e.preventDefault();
				const _this = $( this );
				const search = _this.find( 'input[type="search"]' ).val().trim().toLowerCase();
				const term = parseInt( _this.find( 'select[name="ftf-work-category"]' ).val(), 10 );

				hideItems( module.find( '.ftf_work' ) );
				const filteredWorks = filterWorks( search, term, module );

				if ( filteredWorks.length > 0 ) {
					hideEmptyMessage( module );
					animateItems( filteredWorks, module );
				} else {
					showEmptyMessage( module );
				}
			} );
		};

		const filterWorks = ( search, term, module ) => {
			let filteredWorks = generateTokens( module );
			const filteredWorksElems = [];

			if ( '' !== search ) {
				filteredWorks = filteredWorks.filter( ( token ) => token.title.includes( search ) );
			}

			if ( 0 !== term ) {
				filteredWorks = filteredWorks.filter( ( token ) => token.terms.includes( term ) );
			}

			filteredWorks.forEach( ( item ) => {
				filteredWorksElems.push( $( `#${ item.id }` ) );
			} );

			return filteredWorksElems;
		};

		const hideItems = ( items ) => {
			items.each( function() {
				$( this ).css( 'display', 'none' );
				$( this ).removeClass( 'active' );
			} );
		};

		const animateItems = ( items, module ) => {
			items.forEach( ( item ) => {
				$( item ).addClass( 'active' );
			} );

			module.find( '.ftf_work.active' ).each( function( i ) {
				const $item = $( this );
				setTimeout( function() {
					$item.fadeIn( 400 );
				}, 300 * i );
			} );
		};

		const showEmptyMessage = ( module ) => {
			module.find( '.ftf-module-works__empty-results' ).fadeIn();
		};

		const hideEmptyMessage = ( module ) => {
			module.find( '.ftf-module-works__empty-results' ).fadeOut();
		};

		return {
			init,
		};
	} )();

	$( function() {
		const modules = $( '.ftf-module-works' );

		modules.each( function() {
			workModule.init( $( this ) );
		} );
	} );
}( jQuery ) );
