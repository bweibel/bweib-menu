<?php
if (!defined('ABSPATH')) exit;

/**
 * Ensure ACF loads field groups from this plugin’s /acf-json folder.
 */
add_filter('acf/settings/load_json', function ($paths) {
	$paths[] = BREWERY_MENU_PATH . 'acf-json';
	return $paths;
});

/**
 * Optional: ensure saving JSON goes into the plugin too.
 * You may prefer to NOT do this if your team edits fields in the WP admin,
 * since plugin folders are sometimes not writable in production.
 */
add_filter('acf/settings/save_json', function ($path) {
	return BREWERY_MENU_PATH . 'acf-json';
});
