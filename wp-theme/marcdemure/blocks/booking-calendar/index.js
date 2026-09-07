( function ( blocks, element, serverSideRender, blockEditor ) {
	'use strict';
	blocks.registerBlockType( 'marcdemure/booking-calendar', {
		edit: function () {
			return element.createElement( 'div', blockEditor.useBlockProps(),
				element.createElement( serverSideRender, { block: 'marcdemure/booking-calendar' } ) );
		},
		save: function () { return null; },
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor );
