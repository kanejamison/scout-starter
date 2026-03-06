/**
 * Mobile navigation toggle.
 *
 * @package Scout_Starter
 */
( function() {
	const toggle = document.querySelector( '.menu-toggle' );
	const nav    = document.querySelector( '.main-navigation' );

	if ( ! toggle || ! nav ) {
		return;
	}

	toggle.addEventListener( 'click', function() {
		nav.classList.toggle( 'toggled' );
		const expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
		toggle.setAttribute( 'aria-expanded', ! expanded );
	} );

	// Close menu on escape key.
	document.addEventListener( 'keydown', function( e ) {
		if ( e.key === 'Escape' && nav.classList.contains( 'toggled' ) ) {
			nav.classList.remove( 'toggled' );
			toggle.setAttribute( 'aria-expanded', 'false' );
			toggle.focus();
		}
	} );
} )();
