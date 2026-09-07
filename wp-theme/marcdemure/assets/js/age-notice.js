/**
 * The eighteen-and-over notice.
 *
 * Everything below is an overlay on top of a page that has already rendered,
 * so nothing here is load-bearing: with JavaScript off the site still works,
 * and a crawler sees the page rather than a wall.
 */
( function () {
	'use strict';

	var config = window.mdAgeNotice;

	if ( ! config || document.cookie.indexOf( config.cookie + '=1' ) !== -1 ) {
		return;
	}

	function remember() {
		var expires = new Date( Date.now() + config.days * 864e5 ).toUTCString();
		document.cookie =
			config.cookie + '=1; expires=' + expires + '; path=/; SameSite=Lax' +
			( 'https:' === location.protocol ? '; Secure' : '' );
	}

	var veil = document.createElement( 'div' );
	veil.className = 'md-age';
	veil.setAttribute( 'role', 'dialog' );
	veil.setAttribute( 'aria-modal', 'true' );
	veil.setAttribute( 'aria-labelledby', 'md-age-title' );

	var card = document.createElement( 'div' );
	card.className = 'md-age__card';

	var title = document.createElement( 'h2' );
	title.id = 'md-age-title';
	title.className = 'md-age__title';
	title.textContent = config.title;

	var body = document.createElement( 'p' );
	body.className = 'md-age__body';
	body.textContent = config.body;

	var actions = document.createElement( 'p' );
	actions.className = 'md-age__actions';

	var yes = document.createElement( 'button' );
	yes.type = 'button';
	yes.className = 'md-btn';
	yes.textContent = config.confirm;

	var no = document.createElement( 'a' );
	no.className = 'md-btn md-btn--quiet';
	no.href = config.exitUrl;
	no.rel = 'nofollow noopener';
	no.textContent = config.leave;

	actions.appendChild( yes );
	actions.appendChild( no );
	card.appendChild( title );
	card.appendChild( body );
	card.appendChild( actions );
	veil.appendChild( card );

	function dismiss() {
		remember();
		veil.remove();
		document.documentElement.style.removeProperty( 'overflow' );
		if ( lastFocus && lastFocus.focus ) {
			lastFocus.focus();
		}
	}

	yes.addEventListener( 'click', dismiss );

	// Escape reads as "no" here, so it leaves the notice standing.
	veil.addEventListener( 'keydown', function ( e ) {
		if ( 'Tab' !== e.key ) {
			return;
		}
		// Keep focus inside the notice while it is up.
		var focusable = [ yes, no ];
		var first = focusable[ 0 ];
		var last = focusable[ focusable.length - 1 ];

		if ( e.shiftKey && document.activeElement === first ) {
			e.preventDefault();
			last.focus();
		} else if ( ! e.shiftKey && document.activeElement === last ) {
			e.preventDefault();
			first.focus();
		}
	} );

	var lastFocus = document.activeElement;

	document.documentElement.style.overflow = 'hidden';
	document.body.appendChild( veil );
	yes.focus();
} )();
