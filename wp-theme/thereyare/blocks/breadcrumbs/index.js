/**
 * Editor registration for thereyare/breadcrumbs.
 *
 * Written against the globals WordPress already ships so the theme needs no
 * build step: no npm, no webpack, nothing to compile before deploying. The
 * preview is rendered by PHP through ServerSideRender, which means the editor
 * shows the same trail the visitor will see.
 */
( function ( blocks, blockEditor, serverSideRender, element ) {
	'use strict';

	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;

	blocks.registerBlockType( 'thereyare/breadcrumbs', {
		edit: function ( props ) {
			return el(
				'div',
				useBlockProps(),
				el( serverSideRender, {
					block: 'thereyare/breadcrumbs',
					attributes: props.attributes,
				} )
			);
		},
		// Dynamic block: the front end is rendered by render.php.
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.serverSideRender, window.wp.element );
