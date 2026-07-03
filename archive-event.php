<?php get_header(); ?>

<!-- Hero -->
<section id="events-archive-hero" class="hero hero-simple">
    <div class="container container-sm">
        <div class="breadcrumbs">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
        <h1 class="title-accent"><?= esc_html(post_type_archive_title('', false)); ?></h1>
        <?php if (get_the_archive_description()) : ?>
            <div class="description formatted">
                <?= wp_kses_post(get_the_archive_description()); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Events -->
<section id="events-archive-loop" class="events-archive">
    <div class="container container-lg">
        <?php if (have_posts()) : ?>
            <div class="event-cards">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/item', 'event', array(
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
                <p><?= esc_html__('No events found.', 'around-the-wereld'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
