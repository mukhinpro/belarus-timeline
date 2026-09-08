/**
 * Editor registration for marcdemure/gift-chooser.
 *
 * Rendered by PHP through ServerSideRender so the editor shows the real
 * catalogue — including the setup notice when no Stripe links exist yet,
 * which is exactly when someone is most likely to be looking at it.
 */
( function ( blocks, blockEditor, serverSideRender, element ) {
	'use strict';

	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;

	blocks.registerBlockType( 'marcdemure/gift-chooser', {
		edit: function ( props ) {
			return el(
				'div',
				useBlockProps(),
				el( serverSideRender, {
					block: 'marcdemure/gift-chooser',
					attributes: props.attributes,
				} )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.serverSideRender, window.wp.element );
