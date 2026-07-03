<!-- Latest Events -->
<?php
$post_id = get_the_ID();
$amount = !empty($args['amount']) ? absint($args['amount']) : 4;
$title = !empty($args['title']) ? $args['title'] : __('Agenda', 'around-the-wereld');
$description = array_key_exists('description', $args) ? $args['description'] : __('Live music, shared tables and neighbourhood moments. There is always something happening.', 'around-the-wereld');
$archive_title = !empty($args['archive_title']) ? $args['archive_title'] : __('Want more?', 'around-the-wereld');
$archive_label = !empty($args['archive_label']) ? $args['archive_label'] : __('View agenda', 'around-the-wereld');
$show_archive_link = array_key_exists('show_archive_link', $args) ? (bool) $args['show_archive_link'] : true;
$events_amount = $show_archive_link ? max(1, $amount - 1) : $amount;
$archive_link = get_post_type_archive_link('event');
$now = current_time('mysql');

if (function_exists('apply_filters') && has_filter('wpml_current_language')) {
    $current_lang = apply_filters('wpml_current_language', null);
} else {
    $current_lang = '';
}

$base_query_args = array(
    'post_type' => 'event',
    'post_status' => 'publish',
    'post__not_in' => is_singular('event') ? array(get_the_ID()) : array(),
    'suppress_filters' => false,
);

if ($current_lang) {
    $base_query_args['lang'] = $current_lang;
}

$upcoming_events = get_posts(array_merge($base_query_args, array(
    'numberposts' => $events_amount,
    'meta_key' => 'event_start_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'event_start_date',
            'value' => $now,
            'compare' => '>=',
            'type' => 'DATETIME',
        ),
    ),
)));

$events = $upcoming_events;
$remaining_events = $events_amount - count($events);

if ($remaining_events > 0) {
    $past_events = get_posts(array_merge($base_query_args, array(
        'numberposts' => $remaining_events,
        'post__not_in' => array_merge($base_query_args['post__not_in'], wp_list_pluck($events, 'ID')),
        'meta_key' => 'event_start_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => 'event_start_date',
                'value' => $now,
                'compare' => '<',
                'type' => 'DATETIME',
            ),
        ),
    )));

    $events = array_merge($events, $past_events);
}
?>

<?php if ($events) : ?>
    <section id="latest-events-<?= esc_attr($post_id); ?>" class="latest-events">
        <div class="container container-lg">
            <div class="latest-events-inner">
                <header class="latest-events-header">
                    <h2 class="h3-size"><?= esc_html($title); ?></h2>
                    <?php if ($description) : ?>
                        <p><?= wp_kses_post($description); ?></p>
                    <?php endif; ?>
                </header>

                <div class="event-cards">
                    <?php foreach ($events as $index => $post) :
                        setup_postdata($post); ?>

                        <?php get_template_part('template-parts/item', 'event', array(
                            'index' => $index,
                        )); ?>

                    <?php endforeach; ?>

                    <?php if ($show_archive_link && $archive_link) : ?>
                        <div class="event-archive-action">
                            <?php if ($archive_title) : ?>
                                <h3 class="h4-size"><?= esc_html($archive_title); ?></h3>
                            <?php endif; ?>
                            <a href="<?= esc_url($archive_link); ?>" class="btn btn-secondary btn-icon-arrow-right icon-after">
                                <?= esc_html($archive_label); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
