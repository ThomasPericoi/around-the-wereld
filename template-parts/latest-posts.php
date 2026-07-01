<!-- Latest Posts  -->
<?php
$post_id = get_the_ID();
$amount = !empty($args['amount']) ? absint($args['amount']) : 3;
$title = !empty($args['title']) ? $args['title'] : __('Latest posts', 'around-the-wereld');

if (function_exists('apply_filters') && has_filter('wpml_current_language')) {
    $current_lang = apply_filters('wpml_current_language', null);
} else {
    $current_lang = '';
}

$args_query = array(
    'numberposts' => wp_is_mobile() ? min(2, $amount) : $amount,
    'post_type' => 'post',
    'post__not_in' => is_single() ? array(get_the_ID()) : array(),
    'suppress_filters' => false,
);

if ($current_lang) {
    $args_query['lang'] = $current_lang;
}

$latest = get_posts($args_query); ?>

<?php if ($latest) : ?>
    <section id="latest-posts-<?= esc_attr($post_id); ?>" class="latest-posts">
        <div class="container container-lg">
            <h2 class="h3-size"><?= esc_html($title); ?></h2>
            <div class="grid grid-<?= esc_attr($amount); ?> posts">
                <?php foreach ($latest as $post) :
                    setup_postdata($post); ?>

                    <?php get_template_part('template-parts/item', 'post'); ?>

                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
