/**
 * FiveTwoFive Contact Form — progressive enhancement.
 *
 * Intercepts the form and submits it to the REST endpoint via fetch, showing an
 * inline notice without a page reload. With JavaScript disabled the form simply
 * posts to admin-post.php (its native action), so submission still works.
 */
( function () {
	'use strict';

	var settings = window.FiveTwoFiveContactForm || {};
	if ( ! settings.endpoint ) {
		return;
	}

	var forms = document.querySelectorAll( '.ftf-contact-form' );

	Array.prototype.forEach.call( forms, function ( form ) {
		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();

			var button = form.querySelector( '[type="submit"]' );
			if ( button ) {
				button.disabled = true;
			}

			var body = new URLSearchParams( new FormData( form ) );

			var headers = { Accept: 'application/json' };
			if ( settings.nonce ) {
				// Send WordPress's REST nonce so a logged-in visitor's request
				// runs in their own user context and the form nonce verifies.
				headers[ 'X-WP-Nonce' ] = settings.nonce;
			}

			fetch( settings.endpoint, {
				method: 'POST',
				body: body,
				credentials: 'same-origin',
				headers: headers
			} )
				.then( function ( response ) {
					return response.json().catch( function () {
						return {};
					} );
				} )
				.then( function ( data ) {
					var ok = !! ( data && data.ok );
					showNotice( form, ok, ( data && data.message ) || settings.error );
					if ( ok ) {
						form.reset();
					}
				} )
				.catch( function () {
					showNotice( form, false, settings.error );
				} )
				.then( function () {
					if ( button ) {
						button.disabled = false;
					}
				} );
		} );
	} );

	/**
	 * Show (or update) the inline notice above the form.
	 *
	 * @param {HTMLElement} form    The form element.
	 * @param {boolean}     ok      Whether the submission succeeded.
	 * @param {string}      message The message to display.
	 */
	function showNotice( form, ok, message ) {
		var wrap = form.closest( '.ftf-contact-form-wrap' ) || form.parentNode;
		var notice = wrap.querySelector( '.ftf-contact-form__notice' );

		if ( ! notice ) {
			notice = document.createElement( 'p' );
			wrap.insertBefore( notice, form );
		}

		notice.className =
			'ftf-contact-form__notice ' +
			( ok ? 'ftf-contact-form__notice--success' : 'ftf-contact-form__notice--error' );
		notice.setAttribute( 'role', ok ? 'status' : 'alert' );
		notice.textContent = message || '';
	}
} )();
