<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/index.bundle.css',
	'js' => 'dist/index.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'sholokhov.exchange.ui',
		'ui.entity-selector',
		'ui.tag-selector',
	],
	'skip_core' => true,
];
