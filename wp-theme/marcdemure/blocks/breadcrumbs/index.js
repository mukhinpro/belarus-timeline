/**
 * Editor view. The trail depends on where the template renders, which the
 * editor cannot know, so it asks the server rather than guessing.
 */
( function ( blocks, element, serverSideRender, blockEditor ) {
	'use strict';

	blocks.registerBlockType( 'marcdemure/breadcrumbs', {
		edit: function () {
			return element.createElement(
				'div',
				blockEditor.useBlockProps(),
				element.createElement( serverSideRender, { block: 'marcdemure/breadcrumbs' } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor );
