<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
$post_id = get_the_ID();
$content = trim(get_the_content());
$has_content = $content || has_blocks($post_id);
$has_thumbnail = has_post_thumbnail();
$thumbnail = $has_thumbnail ? get_post(get_post_thumbnail_id()) : false;
$thumbnail_caption = $thumbnail ? $thumbnail->post_excerpt : '';
$hero_background = get_field('page_hero_background');
$subtitle = get_field('page_subtitle');
$hero_backgrounds = array('primary', 'primary-dark', 'primary-light', 'secondary', 'secondary-dark', 'secondary-light', 'tertiary', 'tertiary-dark', 'tertiary-light', 'white', 'black', 'content-color');
?>

<!-- Hero -->
<?php
$hero_classes = array(
    'hero',
    'hero-simple',
    $has_thumbnail ? 'hero-has-media' : false,
    in_array($hero_background, $hero_backgrounds, true) ? 'hero-bg-' . $hero_background : false,
);
$hero_classes_attr = implode(' ', array_filter($hero_classes));
?>
<section id="hero-<?= esc_attr($post_id); ?>" class="<?= esc_attr($hero_classes_attr); ?>">
    <div class="container container-sm">
        <div class="breadcrumbs">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
        <h1><?= esc_html(get_the_title()); ?></h1>
        <?php if ($subtitle) : ?>
            <h2 class="p-size"><?= wp_kses_post($subtitle); ?></h2>
        <?php endif; ?>
        <?php if ($has_thumbnail) : ?>
            <figure class="hero-media">
                <?= get_the_post_thumbnail($post_id, 'full'); ?>
                <?php if ($thumbnail_caption) : ?>
                    <figcaption><?= esc_html($thumbnail_caption); ?></figcaption>
                <?php endif; ?>
            </figure>
        <?php endif; ?>
    </div>
</section>

<?php if ($has_content) : ?>
    <!-- Content -->
    <section id="content">
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

<?php endwhile; endif; ?>

<?php get_footer(); ?>
