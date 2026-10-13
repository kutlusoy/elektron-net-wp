( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var getBlockType = wp.blocks.getBlockType;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;
	var RangeControl = wp.components.RangeControl;
	var ServerSideRender = wp.serverSideRender;

	var slugs = [ 'overview', 'height', 'tip-hash', 'hashrate', 'retarget', 'mempool', 'fees', 'halving', 'latest-blocks' ];

	function makeEdit( name ) {
		return function ( props ) {
			var attributes = props.attributes;
			var set = props.setAttributes;
			var controls = [
				el( TextControl, {
					key: 'heading',
					label: __( 'Heading', 'elektron-live-blocks' ),
					help: __( 'Leave empty for the default heading.', 'elektron-live-blocks' ),
					value: attributes.heading,
					onChange: function ( value ) { set( { heading: value } ); }
				} ),
				el( ToggleControl, {
					key: 'showHeading',
					label: __( 'Show heading', 'elektron-live-blocks' ),
					checked: !! attributes.showHeading,
					onChange: function ( value ) { set( { showHeading: value } ); }
				} ),
				el( ToggleControl, {
					key: 'showUpdated',
					label: __( 'Show time of last update', 'elektron-live-blocks' ),
					checked: !! attributes.showUpdated,
					onChange: function ( value ) { set( { showUpdated: value } ); }
				} )
			];
			var type = getBlockType( name );
			if ( type && type.attributes && type.attributes.rows ) {
				controls.push( el( RangeControl, {
					key: 'rows',
					label: __( 'Rows', 'elektron-live-blocks' ),
					min: 1,
					max: 15,
					value: attributes.rows,
					onChange: function ( value ) { set( { rows: value } ); }
				} ) );
			}
			return el(
				'div',
				useBlockProps(),
				el( InspectorControls, null, el( PanelBody, { title: __( 'Settings', 'elektron-live-blocks' ) }, controls ) ),
				el( ServerSideRender, { block: name, attributes: attributes } )
			);
		};
	}

	slugs.forEach( function ( slug ) {
		var name = 'elektron-live/' + slug;
		registerBlockType( name, {
			edit: makeEdit( name ),
			save: function () { return null; }
		} );
	} );
}( window.wp ) );
