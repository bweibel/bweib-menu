<?php
// blocks/menu-display/render.php

use function esc_html;
use function esc_attr;

defined('ABSPATH') || exit;

$defaultSection = isset($attributes['defaultSection']) ? (string) $attributes['defaultSection'] : '';
$showFilters = !empty($attributes['showFilters']);
$twoColDesktop = !empty($attributes['twoColumnDesktop']);

$sections = get_terms([
    'taxonomy' => 'menu_section',
    'hide_empty' => false,
    'orderby' => 'term_order',
]);

$wrapper_attrs = get_block_wrapper_attributes([
    'class' => 'bweib-menu-display' . ($twoColDesktop ? ' is-two-col' : ''),
]);

?>
<div <?= $wrapper_attrs; ?> data-default-section="<?= esc_attr($defaultSection); ?>">
    <?php if ($showFilters): ?>
        <div class="bweib-menu-filters" aria-label="Menu Filters">
            <div class="bweib-menu-tabs" role="tablist">
                <button class="bweib-tab is-active" type="button" data-section="" role="tab"
                    aria-selected="true">All</button>

                <?php foreach ($sections as $term): ?>
                    <button class="bweib-tab" type="button" data-section="<?= esc_attr($term->slug); ?>" role="tab"
                        aria-selected="false">
                        <?= esc_html($term->name); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="bweib-menu-tools">
                <input class="bweib-menu-search" type="search" placeholder="Search menu…" aria-label="Search menu" />

            </div>

            <div class="bweib-menu-chips" aria-label="Dietary & badges">
                <?php
                $chips = [
                    ['dietary', 'gf', 'GF'],
                    ['dietary', 'v', 'V'],
                    ['dietary', 'vg', 'VG'],
                    ['dietary', 'df', 'DF'],
                    ['dietary', 'nf', 'NF'],
                    ['badges', 'new', 'New'],
                    ['badges', 'seasonal', 'Seasonal'],
                    ['badges', 'popular', 'Popular'],
                ];
                foreach ($chips as [$type, $value, $label]):
                    ?>
                    <button type="button" class="bweib-chip" data-filter-type="<?= esc_attr($type); ?>"
                        data-filter-value="<?= esc_attr($value); ?>">
                        <?= esc_html($label); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="bweib-menu-results" data-results>
        <?php

        
        // Initial render: full grouped list (progressive enhancement)
        foreach ($sections as $term) {
$q = new WP_Query([
  'post_type'      => 'menu_item',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'tax_query'      => [
    [
      'taxonomy' => 'menu_section',
      'field'    => 'term_id',
      'terms'    => [$term->term_id],
    ],
  ],
]);


            if (!$q->have_posts()) {
                continue;
            }

            echo '<section class="bweib-menu-section" data-section="' . esc_attr($term->slug) . '">';
            echo '<div ><h3 class="bweib-menu-section-title">' . esc_html($term->name) . '</h3></div>';

            echo '<div class="bweib-menu-items">';
            while ($q->have_posts()) {
                $q->the_post();

                $id = get_the_ID();

                $price_display = (string) get_field('price_display', $id);
                $price_secondary = (string) get_field('price_secondary', $id);
                $desc = (string) get_field('description', $id);
                $mods = (string) get_field('modifiers', $id);
                $dietary = (array) get_field('dietary_flags', $id);
                $spice = (string) get_field('spice_level', $id);
                $badges = (array) get_field('badges', $id);



                ?>
                <article class="bweib-menu-item" data-item-id="<?= esc_attr($id); ?>"
                    data-dietary="<?= esc_attr(implode(',', $dietary)); ?>" data-spice="<?= esc_attr($spice); ?>"
                    data-badges="<?= esc_attr(implode(',', $badges)); ?>">
                    <header class="bweib-menu-item-header">
                        <h4 class="bweib-menu-item-name"><?= esc_html(get_the_title()); ?></h4>
                        <div class="bweib-menu-item-prices">
                            <?php if ($price_display !== ''): ?>
                                <span class="bweib-price"><?= esc_html($price_display); ?></span>
                            <?php endif; ?>
                            <?php if ($price_secondary !== ''): ?>
                                <span class="bweib-price-secondary"><?= esc_html($price_secondary); ?></span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if ($desc !== ''): ?>
                        <div class="bweib-menu-item-desc"><?= wp_kses_post(wpautop($desc)); ?></div>
                    <?php endif; ?>

                    <?php if ($mods !== ''): ?>
                        <div class="bweib-menu-item-mods"><?= wp_kses_post(wpautop($mods)); ?></div>
                    <?php endif; ?>

                    <div class="bweib-menu-item-flags">
                        <?php foreach ($dietary as $flag): ?>
                            <span class="bweib-pill"><?= esc_html(strtoupper($flag)); ?></span>
                        <?php endforeach; ?>
                        <?php if ($spice && $spice !== 'none'): ?>
                            <span class="bweib-pill"><?= esc_html($spice); ?></span>
                        <?php endif; ?>
                        <?php foreach ($badges as $b): ?>
                            <span class="bweib-pill"><?= esc_html($b); ?></span>
                        <?php endforeach; ?>
                    </div>
                </article>
                <?php
            }
            wp_reset_postdata();

            echo '</div></section>';
        }
        ?>
    </div>

    <div class="bweib-menu-loading" hidden data-loading>Loading…</div>
</div>