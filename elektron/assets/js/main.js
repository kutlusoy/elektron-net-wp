( function () {
	'use strict';

	var root = document.documentElement;
	var storageKey = 'elektron-scheme';

	function resolveScheme() {
		var current = root.getAttribute( 'data-scheme' );
		if ( 'light' === current || 'dark' === current ) {
			return current;
		}
		var fallback = root.getAttribute( 'data-default-scheme' );
		if ( 'light' === fallback ) {
			return 'light';
		}
		if ( 'auto' === fallback && window.matchMedia && window.matchMedia( '(prefers-color-scheme: light)' ).matches ) {
			return 'light';
		}
		return 'dark';
	}

	var schemeToggle = document.querySelector( '.scheme-toggle' );
	if ( schemeToggle ) {
		var syncToggle = function () {
			schemeToggle.setAttribute( 'aria-pressed', 'light' === resolveScheme() ? 'true' : 'false' );
		};
		syncToggle();
		schemeToggle.addEventListener( 'click', function () {
			var next = 'light' === resolveScheme() ? 'dark' : 'light';
			root.setAttribute( 'data-scheme', next );
			try {
				window.localStorage.setItem( storageKey, next );
			} catch ( e ) {}
			syncToggle();
		} );
	}

	var nav = document.getElementById( 'site-navigation' );
	var menuToggle = document.querySelector( '.menu-toggle' );
	if ( ! nav || ! menuToggle ) {
		return;
	}

	function setMenu( open ) {
		nav.classList.toggle( 'toggled', open );
		menuToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	}

	menuToggle.addEventListener( 'click', function () {
		setMenu( 'true' !== menuToggle.getAttribute( 'aria-expanded' ) );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && nav.classList.contains( 'toggled' ) ) {
			setMenu( false );
			menuToggle.focus();
		}
	} );

	var l10n = window.elektronL10n || { expand: 'Expand child menu', collapse: 'Collapse child menu' };

	nav.querySelectorAll( '.menu-item-has-children, .page_item_has_children' ).forEach( function ( item ) {
		var submenu = item.querySelector( '.sub-menu, .children' );
		if ( ! submenu ) {
			return;
		}
		var button = document.createElement( 'button' );
		button.type = 'button';
		button.className = 'submenu-toggle';
		button.setAttribute( 'aria-expanded', 'false' );
		button.setAttribute( 'aria-label', l10n.expand );
		button.addEventListener( 'click', function () {
			var open = ! item.classList.contains( 'toggled-on' );
			item.classList.toggle( 'toggled-on', open );
			button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			button.setAttribute( 'aria-label', open ? l10n.collapse : l10n.expand );
		} );
		item.insertBefore( button, submenu );
	} );
}() );
