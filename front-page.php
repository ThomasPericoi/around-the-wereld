<?php get_header(); ?>

<?php get_template_part('template-parts/front-page-hero'); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php if (trim(get_the_content())) : ?>
        <section id="content-<?= esc_attr(get_the_ID()); ?>" class="front-page-content">
            <div class="container container-sm formatted">
                <?php the_content(); ?>
            </div>
        </section>
    <?php endif; ?>
<?php endwhile; endif; ?>

<?php
$latest_events_title = function_exists('get_field') ? get_field('home_latest_events_title') : '';
$latest_events_description = function_exists('get_field') ? get_field('home_latest_events_description') : '';
$latest_events_archive_title = function_exists('get_field') ? get_field('home_latest_events_archive_title') : '';
$latest_events_archive_label = function_exists('get_field') ? get_field('home_latest_events_archive_label') : '';

get_template_part('template-parts/latest', 'events', array(
    'amount' => 4,
    'title' => $latest_events_title ?: __('Agenda', 'around-the-wereld'),
    'description' => $latest_events_description ?: __('Live music, shared tables and neighbourhood moments. There is always something happening.', 'around-the-wereld'),
    'archive_title' => $latest_events_archive_title ?: __('Want more?', 'around-the-wereld'),
    'archive_label' => $latest_events_archive_label ?: __('View agenda', 'around-the-wereld'),
)); ?>

<?php get_template_part('template-parts/cta-section'); ?>

<?php get_footer(); ?>
