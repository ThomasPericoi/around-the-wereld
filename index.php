<?php get_header(); ?>

<?php
$post_id = get_queried_object_id();

if (is_home() && get_option('page_for_posts')) :
    $title = get_the_title(get_option('page_for_posts'));
elseif (is_archive()) :
    $title = get_the_archive_title();
elseif (is_search()) :
    $title = sprintf(__('Search results for "%s"', 'around-the-wereld'), get_search_query(false));
else :
    $title = get_bloginfo('name');
endif;
?>

<!-- Hero -->
<section id="archive-hero-<?= esc_attr($post_id ?: 'posts'); ?>" class="hero hero-simple">
    <div class="container container-sm">
        <div class="breadcrumbs">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
        <h1><?= wp_kses_post($title); ?></h1>
        <?php if (is_archive() && get_the_archive_description()) : ?>
            <div class="description formatted">
                <?= wp_kses_post(get_the_archive_description()); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Loop -->
<section id="archive-loop-<?= esc_attr($post_id ?: 'posts'); ?>" class="archive-loop">
    <div class="container container-lg">
        <?php if (have_posts()) : ?>
            <div class="grid grid-3 posts">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/item', 'post', array(
                        'index' => $wp_query->current_post,
                    )); ?>
                <?php endwhile; ?>
            </div>

            <?php the_posts_pagination(array(
                'prev_text' => '<span class="icon icon-left" aria-hidden="true"></span><span class="sr-only">' . esc_html__('Previous page', 'around-the-wereld') . '</span>',
                'next_text' => '<span class="icon icon-right" aria-hidden="true"></span><span class="sr-only">' . esc_html__('Next page', 'around-the-wereld') . '</span>',
            )); ?>
        <?php else : ?>
            <div class="formatted">
                <p><?= esc_html__('No content found.', 'around-the-wereld'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
