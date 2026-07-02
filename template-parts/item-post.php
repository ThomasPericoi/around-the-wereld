<?php
$categories = get_the_category();
$primary_category = !empty($categories) ? $categories[0] : false;
$tags = get_the_tags();
$tags_name = array();
$post_index = isset($args['index']) ? absint($args['index']) : 0;
$accent_classes = array('post-accent-primary', 'post-accent-secondary', 'post-accent-tertiary');
$tilt_classes = array('post-tilt-left', 'post-tilt-right');
$post_classes = array(
    'element',
    'post',
    $primary_category ? $primary_category->slug : false,
    has_post_thumbnail() ? 'has-thumbnail' : 'no-thumbnail',
    !has_post_thumbnail() ? $accent_classes[$post_index % count($accent_classes)] : false,
    $tilt_classes[$post_index % count($tilt_classes)],
);
$meta_items = array(
    $primary_category ? $primary_category->name : false,
);

if ($tags) {
    foreach ($tags as $tag) {
        $tags_name[] = $tag->name;
    }

    $meta_items[] = implode(', ', $tags_name);
}
?>
<a href="<?= esc_url(get_the_permalink()); ?>" class="<?= esc_attr(implode(' ', array_map('sanitize_html_class', array_filter($post_classes)))); ?>">
    <?php if (has_post_thumbnail()) : ?>
        <div class="thumbnail" style="background-image: url('<?= esc_url(get_the_post_thumbnail_url(null, 'large')); ?>');">
        </div>
    <?php else : ?>
        <div class="thumbnail placeholder" aria-hidden="true"></div>
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
