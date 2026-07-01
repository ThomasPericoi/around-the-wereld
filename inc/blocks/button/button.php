<?php

/**
 * Button Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during backend preview render.
 * @param   int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param   array $context The context provided to the block by the post or it's parent block.
 */

$classes = array('button-block');
$classes_attr  = implode(' ', $classes);
if (!empty($block['className'])) {
    $classes_attr .= ' ' . implode(' ', array_map('sanitize_html_class', explode(' ', $block['className'])));
}
$styles = array();
$styles_attr  = implode('; ', $styles);
?>

<!-- Block - Button -->
<section class="<?= esc_attr($classes_attr); ?>" style="<?= esc_attr($styles_attr); ?>">
    <?php atw_render_cta('block'); ?>
</section>
