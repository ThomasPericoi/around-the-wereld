<?php
$categories = get_the_category();
$primary_category = !empty($categories) ? $categories[0] : false;
$tags = get_the_tags();
$tags_name = array();
$meta_items = array(
    sprintf(__('By %s', 'around-the-wereld'), get_the_author_meta('display_name')),
    $primary_category ? $primary_category->name : false,
);

if ($tags) {
    foreach ($tags as $tag) {
        $tags_name[] = $tag->name;
    }

    $meta_items[] = implode(', ', $tags_name);
}
?>
<a href="<?= esc_url(get_the_permalink()); ?>" class="element post <?= $primary_category ? esc_attr($primary_category->slug) : ''; ?>">
    <?php if (has_post_thumbnail()) : ?>
        <div class="thumbnail" style="background-image: url('<?= esc_url(get_the_post_thumbnail_url(null, 'large')); ?>');">
        </div>
    <?php endif; ?>
    <div class="content">
        <h3 class="h4-size title"><?= esc_html(get_the_title()); ?></h3>
        <div class="metas">
            <?= esc_html(implode(' | ', array_filter($meta_items))); ?>
        </div>
        <?php if (has_excerpt()) : ?>
            <p><?= esc_html(get_the_excerpt()); ?></p>
        <?php endif; ?>
        <span class="btn btn-primary btn-icon-chevron-right icon-before" aria-hidden="true"><?= esc_html__('Read more', 'around-the-wereld'); ?></span>
        <span class="sr-only"><?= sprintf(esc_html__('Read more about %s', 'around-the-wereld'), esc_html(get_the_title())); ?></span>
    </div>
</a>
