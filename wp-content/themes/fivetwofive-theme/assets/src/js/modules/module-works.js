( function() {
	'use strict';

	const workModule = ( () => {
		const init = ( module ) => {
			if ( hasForm( module ) ) {
				searchFilterInit( module );
			}
		};

		const hasForm = ( module ) => {
			return null !== module.querySelector( '.ftf-form' );
		};

		const generateTokens = ( module ) => {
			const items = module.querySelectorAll( '.ftf_work' );
			const tokens = [];

			items.forEach( ( item ) => {
				const itemTermLinks = item.querySelectorAll( '.card__categories a' );
				const itemTermIds = [];

				itemTermLinks.forEach( ( link ) => {
					itemTermIds.push( parseInt( link.dataset.id, 10 ) );
				} );

				tokens.push( {
					id: item.id,
					title: item.querySelector( '.card__title' )?.textContent.toLowerCase() ?? '',
					terms: itemTermIds,
				} );
			} );

			return tokens;
		};

		const searchFilterInit = ( module ) => {
			module.querySelector( '.ftf-form' ).addEventListener( 'submit', ( e ) => {
				e.preventDefault();
				const search = e.currentTarget.querySelector( 'input[type="search"]' ).value.trim().toLowerCase();
				const term = parseInt( e.currentTarget.querySelector( 'select[name="ftf-work-category"]' ).value, 10 );

				hideItems( module.querySelectorAll( '.ftf_work' ) );
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

			if ( '' !== search ) {
				filteredWorks = filteredWorks.filter( ( token ) => token.title.includes( search ) );
			}

			if ( 0 !== term ) {
				filteredWorks = filteredWorks.filter( ( token ) => token.terms.includes( term ) );
			}

			return filteredWorks
				.map( ( item ) => document.getElementById( item.id ) )
				.filter( Boolean );
		};

		const hideItems = ( items ) => {
			items.forEach( ( item ) => {
				item.style.display = 'none';
				item.style.opacity = '';
				item.classList.remove( 'active' );
			} );
		};

		const animateItems = ( items, module ) => {
			items.forEach( ( item ) => {
				item.classList.add( 'active' );
			} );

			module.querySelectorAll( '.ftf_work.active' ).forEach( ( item, i ) => {
				setTimeout( () => {
					item.style.opacity = '0';
					item.style.display = '';
					// Trigger reflow so the transition fires from opacity 0.
					item.getBoundingClientRect();
					item.style.opacity = '1';
				}, 300 * i );
			} );
		};

		const showEmptyMessage = ( module ) => {
			const msg = module.querySelector( '.ftf-module-works__empty-results' );
			if ( msg ) {
				msg.style.display = '';
			}
		};

		const hideEmptyMessage = ( module ) => {
			const msg = module.querySelector( '.ftf-module-works__empty-results' );
			if ( msg ) {
				msg.style.display = 'none';
			}
		};

		return {
			init,
		};
	} )();

	document.addEventListener( 'DOMContentLoaded', () => {
		document.querySelectorAll( '.ftf-module-works' ).forEach( ( module ) => {
			workModule.init( module );
		} );
	} );
}() );
