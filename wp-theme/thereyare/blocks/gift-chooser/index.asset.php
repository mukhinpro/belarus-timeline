<?php
/**
 * Dependency manifest for the gift chooser editor script.
 * Written by hand: the theme has no build step and the script uses only
 * globals WordPress already enqueues.
 */

return [
	'dependencies' => [
		'wp-blocks',
		'wp-block-editor',
		'wp-element',
		'wp-server-side-render',
	],
	'version'      => '1.0.0',
];
