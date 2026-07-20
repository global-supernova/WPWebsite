/**
 * Supernova theme navigation: toggle the mobile menu.
 *
 * @package Supernova
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var nav = document.getElementById( 'site-navigation' );
		if ( ! nav ) {
			return;
		}

		var toggle = nav.querySelector( '.menu-toggle' );
		var menu = nav.querySelector( 'ul' );
		if ( ! toggle || ! menu ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var isOpen = nav.classList.toggle( 'toggled' );
			toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );

		// Close the menu when a link is clicked (mobile).
		menu.addEventListener( 'click', function ( event ) {
			if ( event.target.tagName === 'A' && nav.classList.contains( 'toggled' ) ) {
				nav.classList.remove( 'toggled' );
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	} );
}() );
