<?php

/**
 * PDF Viewer Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during backend preview render.
 * @param   int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param   array $context The context provided to the block by the post or it's parent block.
 */

$file = get_field('file');

$height = get_field('height') ?: '600px';

$classes = array('pdf-viewer-block');
$classes_attr  = implode(' ', $classes);
if (!empty($block['className'])) {
    $classes_attr .= ' ' . implode(' ', array_map('sanitize_html_class', explode(' ', $block['className'])));
}
$styles = array();
$styles_attr  = implode('; ', $styles);
?>

<!-- Block - PDF Viewer -->
<section class="<?= esc_attr($classes_attr); ?>" style="<?= esc_attr($styles_attr); ?>">
    <?php if ($file) : ?>
        <iframe src="<?= esc_url(get_template_directory_uri() . '/assets/js/pdfjs/web/viewer.html?file=' . rawurlencode($file)); ?>" width="100%" height="<?= esc_attr($height); ?>" title="<?= esc_attr__('PDF viewer', 'around-the-wereld'); ?>"></iframe>
        <a href="<?= esc_url($file); ?>" class="btn btn-primary btn-icon-download" target="_blank" rel="noopener noreferrer">
            <?= esc_html__('Download PDF', 'around-the-wereld'); ?>
        </a>
    <?php endif; ?>
</section>
