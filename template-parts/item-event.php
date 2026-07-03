<?php
$event_id = get_the_ID();
$event_index = isset($args['index']) ? absint($args['index']) : 0;
$event_date = get_field('event_start_date', $event_id);
$event_timestamp = $event_date ? strtotime($event_date) : false;
$event_location = get_field('event_location', $event_id);
$event_subtitle = get_field('event_subtitle', $event_id) ?: get_the_excerpt();
$is_past_event = $event_timestamp && $event_timestamp < current_time('timestamp');
$accent_classes = array('event-accent-primary', 'event-accent-secondary', 'event-accent-tertiary');
$tilt_classes = array('event-tilt-left', 'event-tilt-right');
$event_classes = array(
    'event-card',
    $is_past_event ? 'event-is-past' : 'event-is-upcoming',
    $accent_classes[$event_index % count($accent_classes)],
    $tilt_classes[$event_index % count($tilt_classes)],
);
$event_date_label = $event_timestamp ? date_i18n('D j M - H:i', $event_timestamp) : '';
$event_date_label = function_exists('mb_strtoupper') ? mb_strtoupper($event_date_label) : strtoupper($event_date_label);
?>

<a href="<?= esc_url(get_the_permalink()); ?>" class="<?= esc_attr(implode(' ', array_filter($event_classes))); ?>">
    <span class="event-status">
        <?= esc_html($is_past_event ? __('Past', 'around-the-wereld') : __('Upcoming', 'around-the-wereld')); ?>
    </span>

    <?php if ($event_timestamp) : ?>
        <time class="event-date" datetime="<?= esc_attr(date('c', $event_timestamp)); ?>">
            <?= esc_html($event_date_label); ?>
        </time>
    <?php endif; ?>

    <h3 class="h4-size event-title"><?= esc_html(get_the_title()); ?></h3>

    <?php if ($event_subtitle) : ?>
        <p><?= wp_kses_post($event_subtitle); ?></p>
    <?php endif; ?>

    <?php if ($event_location) : ?>
        <span class="event-location"><?= esc_html($event_location); ?></span>
    <?php endif; ?>
</a>
