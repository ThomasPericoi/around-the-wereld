<?php

/**
 * Gallery Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during backend preview render.
 * @param   int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param   array $context The context provided to the block by the post or it's parent block.
 */

$classes = array('gallery-block', 'js-alwaysInView');
$classes_attr  = implode(' ', $classes);
if (!empty($block['className'])) {
    $classes_attr .= ' ' . implode(' ', array_map('sanitize_html_class', explode(' ', $block['className'])));
}
$styles = array();
$styles_attr  = implode('; ', $styles);
?>

<!-- Block - Gallery -->
<section class="<?= esc_attr($classes_attr); ?>" style="<?= esc_attr($styles_attr); ?>">
    <?php get_template_part('template-parts/gallery', '', array(
        'images' => get_field('gallery'),
        'description' => get_field('description') ?: esc_attr__('Gallery', 'around-the-wereld'),
    )); ?>
</section>
