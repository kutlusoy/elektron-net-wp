( function () {
	var root = document.documentElement;
	var palettes = [ 'blue', 'neon', 'synthwave', 'matrix', 'rust', 'iron', 'violet' ];
	var levels = [ 'off', 'subtle', 'full' ];
	try {
		var scheme = window.localStorage.getItem( 'elektron-scheme' );
		if ( 'light' === scheme || 'dark' === scheme ) {
			root.setAttribute( 'data-scheme', scheme );
		}
		var palette = window.localStorage.getItem( 'elektron-palette' );
		if ( palettes.indexOf( palette ) > -1 ) {
			root.setAttribute( 'data-palette', palette );
		}
		var fx = window.localStorage.getItem( 'elektron-fx' );
		if ( levels.indexOf( fx ) > -1 ) {
			root.setAttribute( 'data-fx', fx );
		}
	} catch ( e ) {}
}() );
