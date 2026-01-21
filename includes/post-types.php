<?php
if (!defined('ABSPATH')) exit;

/**
 * Public function so activation hook can call it.
 */
function brewery_menu_register_post_types_and_taxonomies(): void {

	/**
	 * CPT: Menu Item
	 */
	register_post_type('menu_item', [
		'labels' => [
			'name'               => __('Menu Items', 'brewery-menu'),
			'singular_name'      => __('Menu Item', 'brewery-menu'),
			'add_new'            => __('Add Menu Item', 'brewery-menu'),
			'add_new_item'       => __('Add New Menu Item', 'brewery-menu'),
			'edit_item'          => __('Edit Menu Item', 'brewery-menu'),
			'new_item'           => __('New Menu Item', 'brewery-menu'),
			'view_item'          => __('View Menu Item', 'brewery-menu'),
			'search_items'       => __('Search Menu Items', 'brewery-menu'),
			'not_found'          => __('No menu items found.', 'brewery-menu'),
			'not_found_in_trash' => __('No menu items found in Trash.', 'brewery-menu'),
			'all_items'          => __('All Menu Items', 'brewery-menu'),
			'menu_name'          => __('Menu', 'brewery-menu'),
		],
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'has_archive'         => false,
		'rewrite'             => ['slug' => 'menu'],
		'menu_icon'           => 'dashicons-clipboard',
		'supports'            => ['title'],
		'hierarchical'        => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	]);

	/**
	 * Taxonomy: Menu Section (hierarchical)
	 */
	register_taxonomy('menu_section', ['menu_item'], [
		'labels' => [
			'name'              => __('Menu Sections', 'brewery-menu'),
			'singular_name'     => __('Menu Section', 'brewery-menu'),
			'search_items'      => __('Search Menu Sections', 'brewery-menu'),
			'all_items'         => __('All Menu Sections', 'brewery-menu'),
			'parent_item'       => __('Parent Menu Section', 'brewery-menu'),
			'parent_item_colon' => __('Parent Menu Section:', 'brewery-menu'),
			'edit_item'         => __('Edit Menu Section', 'brewery-menu'),
			'update_item'       => __('Update Menu Section', 'brewery-menu'),
			'add_new_item'      => __('Add New Menu Section', 'brewery-menu'),
			'new_item_name'     => __('New Menu Section Name', 'brewery-menu'),
			'menu_name'         => __('Menu Sections', 'brewery-menu'),
		],
		'public'            => true,
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => ['slug' => 'menu-section'],
	]);

	/**
	 * Taxonomy: Menu Tag (non-hierarchical)
	 */
	register_taxonomy('menu_tag', ['menu_item'], [
		'labels' => [
			'name'                       => __('Menu Tags', 'brewery-menu'),
			'singular_name'              => __('Menu Tag', 'brewery-menu'),
			'search_items'               => __('Search Menu Tags', 'brewery-menu'),
			'popular_items'              => __('Popular Menu Tags', 'brewery-menu'),
			'all_items'                  => __('All Menu Tags', 'brewery-menu'),
			'edit_item'                  => __('Edit Menu Tag', 'brewery-menu'),
			'update_item'                => __('Update Menu Tag', 'brewery-menu'),
			'add_new_item'               => __('Add New Menu Tag', 'brewery-menu'),
			'new_item_name'              => __('New Menu Tag Name', 'brewery-menu'),
			'separate_items_with_commas' => __('Separate tags with commas', 'brewery-menu'),
			'add_or_remove_items'        => __('Add or remove tags', 'brewery-menu'),
			'choose_from_most_used'      => __('Choose from the most used tags', 'brewery-menu'),
			'menu_name'                  => __('Menu Tags', 'brewery-menu'),
		],
		'public'            => true,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => ['slug' => 'menu-tag'],
	]);
}

add_action('init', 'brewery_menu_register_post_types_and_taxonomies', 5);
