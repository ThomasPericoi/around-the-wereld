<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
$post_id = get_the_ID();
$content = trim(get_the_content());
$has_thumbnail = has_post_thumbnail();
$thumbnail = $has_thumbnail ? get_post(get_post_thumbnail_id()) : false;
$thumbnail_caption = $thumbnail ? $thumbnail->post_excerpt : '';
$categories = get_the_category();
$primary_category = !empty($categories) ? $categories[0] : false;
$tags = get_the_tags();
$hero_background = get_field('page_hero_background');
$hero_backgrounds = array('primary', 'primary-dark', 'primary-light', 'secondary', 'secondary-dark', 'secondary-light', 'tertiary', 'tertiary-dark', 'tertiary-light', 'white', 'black', 'content-color');
$subtitle = get_field('page_subtitle') ?: get_the_excerpt();
?>

<!-- Hero -->
<?php
$hero_classes = array(
    'hero',
    'hero-simple',
    $primary_category ? $primary_category->slug : false,
    $has_thumbnail ? 'hero-has-media' : false,
    in_array($hero_background, $hero_backgrounds, true) ? 'hero-bg-' . $hero_background : false,
);
$hero_classes_attr = implode(' ', array_map('sanitize_html_class', array_filter($hero_classes)));
?>
<section id="hero-<?= esc_attr($post_id); ?>" class="<?= esc_attr($hero_classes_attr); ?>">
    <div class="container container-sm formatted">
        <div class="breadcrumbs">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
        <h1 class="title"><?= esc_html(get_the_title()); ?></h1>
        <?php if ($subtitle) : ?>
            <p class="description"><?= wp_kses_post($subtitle); ?></p>
        <?php endif; ?>
        <?php if ($has_thumbnail) : ?>
            <figure class="hero-media">
                <?php the_post_thumbnail('full'); ?>
                <?php if ($thumbnail_caption) : ?>
                    <figcaption><?= esc_html($thumbnail_caption); ?></figcaption>
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
