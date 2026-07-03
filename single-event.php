<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
$post_id = get_the_ID();
$content = trim(get_the_content());
$event_date = get_field('event_start_date');
$event_timestamp = $event_date ? strtotime($event_date) : false;
$event_location = get_field('event_location');
$event_subtitle = get_field('event_subtitle') ?: get_the_excerpt();
$event_button = atw_get_cta('event_button', $post_id);
$is_past_event = $event_timestamp && $event_timestamp < current_time('timestamp');
$event_date_label = $event_timestamp ? date_i18n('l j F Y - H:i', $event_timestamp) : '';
?>

<!-- Hero -->
<section id="event-hero-<?= esc_attr($post_id); ?>" class="hero hero-simple event-single-hero <?= $is_past_event ? 'event-is-past' : 'event-is-upcoming'; ?>">
    <div class="container container-sm formatted">
        <div class="breadcrumbs">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>

        <span class="event-badge event-status">
            <?= esc_html($is_past_event ? __('Past event', 'around-the-wereld') : __('Upcoming event', 'around-the-wereld')); ?>
        </span>

        <h1 class="title-accent"><?= esc_html(get_the_title()); ?></h1>

        <?php if ($event_subtitle) : ?>
            <p class="description"><?= wp_kses_post($event_subtitle); ?></p>
        <?php endif; ?>

        <dl class="event-single-metas">
            <?php if ($event_timestamp) : ?>
                <div>
                    <dt><?= esc_html__('Date', 'around-the-wereld'); ?></dt>
                    <dd><time datetime="<?= esc_attr(date('c', $event_timestamp)); ?>"><?= esc_html($event_date_label); ?></time></dd>
                </div>
            <?php endif; ?>

            <?php if ($event_location) : ?>
                <div>
                    <dt><?= esc_html__('Location', 'around-the-wereld'); ?></dt>
                    <dd><?= esc_html($event_location); ?></dd>
                </div>
            <?php endif; ?>
        </dl>

        <?php if ($event_button) : ?>
            <div class="btn-wrapper event-single-actions">
                <?= $event_button; ?>
            </div>
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

<?php if ($content) : ?>
    <!-- Content -->
    <section id="event-content-<?= esc_attr($post_id); ?>">
        <div class="container container-sm formatted">
            <?php the_content(); ?>
        </div>
    </section>
<?php endif; ?>

<?php get_template_part('template-parts/latest', 'events', array(
    'amount' => 4,
    'title' => __('More events', 'around-the-wereld'),
    'description' => '',
    'show_archive_link' => false,
)); ?>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
