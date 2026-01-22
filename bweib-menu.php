<?php
/**
 * Plugin Name: bweib Menu
 * Description: Custom menu system for a brewery/restaurant/winery.
 * Version: 0.1.0
 * Author: Ben Weibel
 * Text Domain: bweib-menu
 * Update URI: false
 */

if (!defined('ABSPATH')) exit;

define('BREWERY_MENU_VERSION', '0.1.0');
define('BREWERY_MENU_PATH', plugin_dir_path(__FILE__));
define('BREWERY_MENU_URL', plugin_dir_url(__FILE__));

require_once BREWERY_MENU_PATH . 'includes/post-types.php';
require_once BREWERY_MENU_PATH . 'includes/acf-json.php';
require_once BREWERY_MENU_PATH . 'includes/rest.php';
require_once BREWERY_MENU_PATH . 'includes/blocks.php';

/**
 * Activation: register types then flush rewrites
 */
register_activation_hook(__FILE__, function () {
	// Ensure CPT/tax are registered before flushing
	brewery_menu_register_post_types_and_taxonomies();
	flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function () {
	flush_rewrite_rules();
});
