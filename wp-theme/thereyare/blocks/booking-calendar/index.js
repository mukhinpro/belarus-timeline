( function ( blocks, element, serverSideRender, blockEditor ) {
	'use strict';
	blocks.registerBlockType( 'thereyare/booking-calendar', {
		edit: function () {
			return element.createElement( 'div', blockEditor.useBlockProps(),
				element.createElement( serverSideRender, { block: 'thereyare/booking-calendar' } ) );
		},
		save: function () { return null; },
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor );
