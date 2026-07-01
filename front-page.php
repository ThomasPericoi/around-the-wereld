<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php if (trim(get_the_content())) : ?>
        <section id="content-<?= esc_attr(get_the_ID()); ?>" class="front-page-content">
            <div class="container container-sm formatted">
                <?php the_content(); ?>
            </div>
        </section>
    <?php endif; ?>
<?php endwhile; endif; ?>

<?php get_footer(); ?>
