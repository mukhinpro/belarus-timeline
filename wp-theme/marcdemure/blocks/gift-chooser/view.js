/**
 * Front-end behaviour for the gift certificate chooser.
 *
 * Upgrades a list of links into pick-then-confirm. The cards are already
 * working links when this file has not run, so everything here is an
 * enhancement and nothing here is load-bearing.
 */
( function () {
	'use strict';

	document.querySelectorAll( '.md-gift' ).forEach( function ( root ) {
		var cards = root.querySelectorAll( '.md-gift__card' );
		var bar = root.querySelector( '.md-gift__bar' );
		var chosen = root.querySelector( '[data-chosen]' );
		var go = root.querySelector( '[data-go]' );

		if ( ! cards.length || ! bar || ! chosen || ! go ) {
			return;
		}

		function select( card ) {
			cards.forEach( function ( c ) {
				var on = c === card;
				c.classList.toggle( 'is-chosen', on );
				c.setAttribute( 'aria-current', on ? 'true' : 'false' );
			} );

			chosen.textContent =
				card.dataset.title + ' — $' + Number( card.dataset.price ).toLocaleString( 'en-US' );
			go.href = card.href;
			bar.hidden = false;
		}

		cards.forEach( function ( card ) {
			card.setAttribute( 'aria-current', 'false' );

			card.addEventListener( 'click', function ( e ) {
				// Let people who deliberately open a new tab do exactly that.
				if ( e.metaKey || e.ctrlKey || e.shiftKey || 1 === e.button ) {
					return;
				}
				e.preventDefault();
				select( card );

				// On a phone the confirm bar sits below the fold, and a card
				// that silently highlights offscreen reads as a dead tap.
				if ( window.matchMedia( '(max-width: 780px)' ).matches ) {
					bar.scrollIntoView( { block: 'nearest', behavior: 'smooth' } );
				}
			} );
		} );
	} );
} )();
