( function( $, FTF, ScrollReveal ) {
	'use strict';

	class resourceModule {
		constructor( module ) {
			this.module = module;
			this.itemPerPage = module.dataset.itemPerPage;
			this.paginationContainer = module.querySelector( '.ftf-module__pagination-container' );
			this.statusRegion = this.createStatusRegion();
			this.animationConfig = {
				mobile: false,
				duration: 1000,
				interval: 300,
				reset: false,
				distance: '10px',
			};
		}

		createStatusRegion() {
			const region = document.createElement( 'p' );
			region.setAttribute( 'aria-live', 'polite' );
			region.setAttribute( 'aria-atomic', 'true' );
			region.classList.add( 'screen-reader-text' );
			this.module.insertAdjacentElement( 'afterbegin', region );
			return region;
		}

		init() {
			this.formInit();
			this.paginationInit();
		}

		animateResources() {
			ScrollReveal().reveal( this.module.querySelectorAll( '.card' ), this.animationConfig );
		}

		formInit() {
			this.module.querySelector( '.ftf-form' ).addEventListener( 'submit', ( event ) => {
				event.preventDefault();
				this.fetchResources( 1 );
			} );
		}

		fetchResources( page ) {
			const requestURL = new URL( FTF.restBase );
			requestURL.searchParams.append( '_fields', 'id,date_gmt,ftf_formatted_date,ftf_resource_categories,ftf_resource_tags,title,link,_links,_embedded' );
			requestURL.searchParams.append( 'per_page', this.itemPerPage );
			requestURL.searchParams.append( 'page', page );
			requestURL.searchParams.append( '_embed', 'wp:featuredmedia' );

			const search = this.module.querySelector( '[name="ftf-search-resource"]' ).value;
			const category = this.module.querySelector( '[name="ftf-category-resource"]' )?.value ?? null;

			if ( search ) {
				requestURL.searchParams.append( 'search', search );
			}

			if ( category && category !== '0' ) {
				requestURL.searchParams.append( 'ftf-resource-categories', category );
			}

			this.isLoading();

			$.ajax( { url: requestURL.href } )
				.done( ( data, textStatus, request ) => {
					if ( 'success' === textStatus ) {
						this.updateResources( data );
						this.generatePagination( request.getResponseHeader( 'X-WP-TotalPages' ) );
					}
				} ).always( () => {
					this.isComplete();
				} );
		}

		updateResources( data ) {
			const resourcesWrap = this.module.querySelector( '.ftf-resources-wrap' );

			resourcesWrap.innerHTML = '';

			data.forEach( ( resource ) => {
				resourcesWrap.insertAdjacentHTML( 'beforeend', this.createResource( resource ) );
			} );

			this.animateResources();

			const count = data.length;
			this.statusRegion.textContent = count
				? `${ count } resource${ count !== 1 ? 's' : '' } loaded.`
				: 'No resources found.';
		}

		createResource( resource ) {
			let resourceHTML = '';
			let categories = '';
			let tags = '';
			let image = '';

			if ( resource ) {
				if ( resource?.ftf_resource_categories ) {
					categories += '<ul class="card__categories">';

					resource.ftf_resource_categories.forEach( ( category ) => {
						categories += `<li><a href="${ category.link }">${ category.name }</a></li>`;
					} );

					categories += '</ul>';
				}

				if ( resource?.ftf_resource_tags && resource.ftf_resource_tags.length > 0 ) {
					tags += '<p class="card__tags"><strong>Tags:</strong> ';

					resource.ftf_resource_tags.forEach( ( tag, i ) => {
						if ( i !== 0 ) {
							tags += ',';
						}
						tags += ` <a rel="tag" href="${ tag.link }">${ tag.name }</a>`;
					} );

					tags += '</p>';
				}

				if ( resource._embedded?.[ 'wp:featuredmedia' ]?.[ 0 ]?.media_details?.sizes?.[ 'ftf-resource-thumb' ]?.source_url ) {
					image += `<img width="415" height="245" src="${ resource._embedded[ 'wp:featuredmedia' ][ 0 ].media_details.sizes[ 'ftf-resource-thumb' ].source_url }" class="card__image img-responsive wp-post-image" alt="${ resource.title.rendered }" loading="lazy">`;
				}

				resourceHTML = `
				  <div class="col-md-4 mb-3 mb-md-5">
					<article id="card-${ resource.id }" class="card post-2990 ftf_resource type-ftf_resource status-publish has-post-thumbnail hentry load-hidden">
						<div class="card__top">
							${ categories }
							${ image }
						</div>
					  <div class="card__bottom">
						<header class="card__header m-0">
						  <div class="ftf-post-meta entry-meta"><span class="posted-on"><a href="${ resource.link }" rel="bookmark"><time class="entry-date published" datetime="${ resource.date }">${ resource.ftf_formatted_date }</time></a></span></div>
						  <h3 class="card__title mt-2"><a href="${ resource.link }">${ resource.title.rendered }</a></h3>
						  ${ tags }
						</header>
					  </div>
					</article>
				  </div>
				`;
			}
			return resourceHTML;
		}

		generatePagination( totalPages ) {
			this.paginationContainer.innerHTML = '';

			if ( totalPages <= 1 ) {
				return;
			}

			const currentPage = this.module.dataset.currentPage;
			const paginationNav = this.setupPaginationNav();
			paginationNav.querySelector( '.nav-links' ).innerHTML = this.generatePaginationLinks( currentPage, totalPages );

			this.paginationContainer.insertAdjacentElement( 'beforeend', paginationNav );
		}

		setupPaginationNav() {
			const paginationNav = document.createElement( 'nav' );
			paginationNav.classList.add( 'navigation', 'pagination' );
			paginationNav.setAttribute( 'role', 'navigation' );
			paginationNav.setAttribute( 'aria-label', 'Resources' );

			const paginationHeading = document.createElement( 'h2' );
			paginationHeading.classList.add( 'screen-reader-text' );
			paginationHeading.textContent = 'Resources navigation';

			const paginationLinks = document.createElement( 'div' );
			paginationLinks.classList.add( 'nav-links' );

			paginationNav.insertAdjacentElement( 'afterbegin', paginationHeading );
			paginationNav.insertAdjacentElement( 'beforeend', paginationLinks );

			return paginationNav;
		}

		generatePaginationLinks( current = 1, totalPages ) {
			let paginationLinks = '';
			current = Number.parseInt( current, 10 );

			for ( let index = 1; index <= totalPages; index++ ) {
				if ( index === current ) {
					paginationLinks += `<span aria-current="page" class="page-numbers current">${ index }</span>`;
				} else {
					paginationLinks += `<a class="page-numbers" aria-label="Go to page ${ index }" data-page="${ index }" href="#">${ index }</a>`;
				}
			}
			return paginationLinks;
		}

		paginationInit() {
			this.paginationContainer.addEventListener( 'click', ( event ) => {
				const link = event.target.closest( '.page-numbers[data-page]' );
				if ( ! link ) {
					return;
				}
				event.preventDefault();
				const currentPage = Number.parseInt( link.dataset.page, 10 );
				this.module.dataset.currentPage = currentPage;
				this.fetchResources( currentPage );
			} );
		}

		generateSpinner() {
			return `<div class="fivetwofive-spinner"><div></div><div></div></div>`;
		}

		isLoading() {
			const resourcesWrap = this.module.querySelector( '.ftf-resources-wrap' );

			this.module.querySelector( 'input[type="submit"]' ).setAttribute( 'disabled', 'disabled' );
			resourcesWrap.innerHTML = this.generateSpinner();
			this.statusRegion.textContent = 'Loading resources...';
		}

		isComplete() {
			this.module.querySelector( 'input[type="submit"]' ).removeAttribute( 'disabled' );
		}
	}

	$( function() {
		const modules = document.querySelectorAll( '.ftf-module-resources' );

		modules.forEach( ( module ) => {
			const singleResourceModule = new resourceModule( module );
			singleResourceModule.init();
		} );
	} );
}( jQuery, FiveTwoFive, ScrollReveal ) );
