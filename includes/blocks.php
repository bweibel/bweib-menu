<?php
// includes/blocks.php

namespace BWEIB\Menu;

defined('ABSPATH') || exit;

add_action('init', function () {
    $dir = plugin_dir_path(__DIR__) . 'blocks/menu-display';
    if (file_exists($dir . '/block.json')) {
        register_block_type($dir);
    }
});
