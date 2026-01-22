<?php
// includes/rest.php

namespace BWEIB\Menu;

defined('ABSPATH') || exit;

add_action('rest_api_init', function () {
    register_rest_route('bweib-menu/v1', '/items', [
        'methods'  => 'GET',
        'callback' => __NAMESPACE__ . '\\rest_get_items',
        'permission_callback' => '__return_true',
        'args' => [
            'section'    => ['type' => 'string', 'required' => false],
            'tag'        => ['type' => 'string', 'required' => false],
            'q'          => ['type' => 'string', 'required' => false],
            'available'  => ['type' => 'boolean', 'required' => false],
            'dietary'    => ['type' => 'array', 'required' => false],
            'badges'     => ['type' => 'array', 'required' => false],
            'spice_level'=> ['type' => 'string', 'required' => false],
        ],
    ]);
});

function rest_get_items(\WP_REST_Request $req) {
    $section   = (string) $req->get_param('section');
    $tag       = (string) $req->get_param('tag');
    $q         = (string) $req->get_param('q');
    $available = $req->get_param('available');

    $dietary   = (array) $req->get_param('dietary');
    $badges    = (array) $req->get_param('badges');
    $spice     = (string) $req->get_param('spice_level');

    $tax_query = [];

    if ($section !== '') {
        $tax_query[] = [
            'taxonomy' => 'menu_section',
            'field'    => is_numeric($section) ? 'term_id' : 'slug',
            'terms'    => [$section],
        ];
    }

    if ($tag !== '') {
        $tax_query[] = [
            'taxonomy' => 'menu_tag',
            'field'    => is_numeric($tag) ? 'term_id' : 'slug',
            'terms'    => [$tag],
        ];
    }

    $meta_query = [];

    if ($available === null) {
        // default: only available
        $available = true;
    }
    if ($available) {
        $meta_query[] = [
            'key'     => 'available',
            'value'   => 1,
            'compare' => '=',
        ];
    }

    if (!empty($dietary)) {
        // ACF checkbox stored as serialized array in meta
        foreach ($dietary as $flag) {
            $meta_query[] = [
                'key'     => 'dietary_flags',
                'value'   => '"' . sanitize_text_field($flag) . '"',
                'compare' => 'LIKE',
            ];
        }
    }

    if (!empty($badges)) {
        foreach ($badges as $badge) {
            $meta_query[] = [
                'key'     => 'badges',
                'value'   => '"' . sanitize_text_field($badge) . '"',
                'compare' => 'LIKE',
            ];
        }
    }

    if ($spice !== '' && $spice !== 'none') {
        $meta_query[] = [
            'key'     => 'spice_level',
            'value'   => sanitize_text_field($spice),
            'compare' => '=',
        ];
    }

    $args = [
        'post_type'      => 'menu_item',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        's'              => $q !== '' ? $q : null,
        'tax_query'      => !empty($tax_query) ? $tax_query : null,
        'meta_query'     => !empty($meta_query) ? $meta_query : null,
        'meta_key'       => 'sort_order',
        'orderby'        => [
            'meta_value_num' => 'ASC',
            'title'          => 'ASC',
        ],
    ];

    $query = new \WP_Query($args);

    $items = [];
    foreach ($query->posts as $post) {
        $id = $post->ID;

        $items[] = [
            'id' => $id,
            'title' => get_the_title($id),
            'section' => wp_get_post_terms($id, 'menu_section', ['fields' => 'slugs']),
            'tag' => wp_get_post_terms($id, 'menu_tag', ['fields' => 'slugs']),
            'price_display' => (string) get_field('price_display', $id),
            'price_secondary' => (string) get_field('price_secondary', $id),
            'description' => (string) get_field('description', $id),
            'modifiers' => (string) get_field('modifiers', $id),
            'dietary_flags' => (array) get_field('dietary_flags', $id),
            'spice_level' => (string) get_field('spice_level', $id),
            'badges' => (array) get_field('badges', $id),
            'available' => (bool) get_field('available', $id),
        ];
    }

    return rest_ensure_response([
        'count' => count($items),
        'items' => $items,
    ]);
}
