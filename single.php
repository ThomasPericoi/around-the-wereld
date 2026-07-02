<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
$post_id = get_the_ID();
$content = trim(get_the_content());
$categories = get_the_category();
$primary_category = !empty($categories) ? $categories[0] : false;
$tags = get_the_tags();
?>

<!-- Hero -->
<section id="hero-<?= esc_attr($post_id); ?>" class="hero hero-simple <?= $primary_category ? esc_attr($primary_category->slug) : ''; ?>">
    <div class="container container-sm formatted">
        <div class="breadcrumbs">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
        <h1 class="title"><?= esc_html(get_the_title()); ?></h1>
        <?php if (has_excerpt()) : ?>
            <p class="description"><?= esc_html(get_the_excerpt()); ?></p>
        <?php endif; ?>
        <?php if (has_post_thumbnail()) : ?>
            <figure class="hero-media">
                <?php the_post_thumbnail('full'); ?>
                <?php if (($thumbnail = get_post(get_post_thumbnail_id())) && ($excerpt = $thumbnail->post_excerpt)) : ?>
                    <figcaption><?= esc_html($excerpt); ?></figcaption>
                <?php endif; ?>
            </figure>
        <?php endif; ?>
    </div>
</section>

<!-- Content -->
<?php if ($content) : ?>
    <!-- Content -->
    <section id="content-<?= esc_attr($post_id); ?>">
        <div class="container container-sm formatted">
            <?php the_content(); ?>
            <?php
            wp_link_pages(array(
                'before' => '<nav class="page-links" aria-label="' . esc_attr__('Page navigation', 'around-the-wereld') . '">',
                'after' => '</nav>',
            ));
            ?>
        </div>
    </section>
<?php endif; ?>

<?php get_template_part('template-parts/latest', 'posts', array(
    'amount' => 3,
    'title' => __('Read also', 'around-the-wereld'),
)); ?>

<!-- Tags -->
<?php if ($tags) : ?>
    <section id="tags-<?= esc_attr($post_id); ?>" class="tags">
        <div class="container container-sm">
            <ul class="tags-list">
                <li class="label"><?= esc_html__('Topic(s)', 'around-the-wereld'); ?></li>
                <?php foreach ($tags as $tag) : ?>
                    <li><a href="<?= esc_url(get_tag_link($tag->term_id)); ?>" class="tag"><?= esc_html($tag->name); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
<?php endif; ?>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
