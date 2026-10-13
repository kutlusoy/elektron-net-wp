( function () {
	try {
		var scheme = window.localStorage.getItem( 'elektron-scheme' );
		if ( 'light' === scheme || 'dark' === scheme ) {
			document.documentElement.setAttribute( 'data-scheme', scheme );
		}
	} catch ( e ) {}
}() );
